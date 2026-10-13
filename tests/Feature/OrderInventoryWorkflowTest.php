<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\GoodsIssue;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\QcInspection;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class OrderInventoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $admin;

    private Category $category;

    private Product $prodA;

    private Product $prodB;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'manage_orders']);
        Permission::firstOrCreate(['name' => 'manage_products']);

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->givePermissionTo('manage_orders');
        $this->admin->givePermissionTo('manage_products');

        $this->category = Category::create([
            'name' => 'Ống Kính Sony',
            'slug' => 'ong-kinh-sony',
            'is_active' => true,
        ]);

        $this->prodA = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Sony FE 24-70mm f/2.8 GM II',
            'brand' => 'Sony',
            'price' => 45000000,
            'stock' => 10,
            'reserved_stock' => 0,
            'defective_stock' => 0,
            'status' => 'in_stock',
        ]);

        $this->prodB = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Sony FE 50mm f/1.2 GM',
            'brand' => 'Sony',
            'price' => 42000000,
            'stock' => 5,
            'reserved_stock' => 0,
            'defective_stock' => 0,
            'status' => 'in_stock',
        ]);
    }

    /**
     * Test 4 & 5: Đơn hàng có nhiều sản phẩm hoặc số lượng > 1, xuất kho đúng số lượng.
     */
    public function test_multi_item_order_completion_creates_single_goods_issue_and_deducts_reserved_stock(): void
    {
        // Khách đặt hàng: ProdA x2, ProdB x1
        $this->prodA->decrement('stock', 2);
        $this->prodA->increment('reserved_stock', 2);
        $this->prodB->decrement('stock', 1);
        $this->prodB->increment('reserved_stock', 1);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2610140001',
            'recipient_name' => 'Test User',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '123 Đội Cấn',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'preparing',
            'subtotal' => 132000000,
            'shipping_fee' => 0,
            'total' => 132000000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->prodA->id,
            'product_name' => $this->prodA->name,
            'quantity' => 2,
            'unit_price' => $this->prodA->price,
            'subtotal' => 90000000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->prodB->id,
            'product_name' => $this->prodB->name,
            'quantity' => 1,
            'unit_price' => $this->prodB->price,
            'subtotal' => 42000000,
        ]);

        // Admin hoàn tất đơn hàng
        $response = $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $order), [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $response->assertRedirect();

        // 1. Kiểm tra tồn kho sau khi xuất
        $this->assertEquals(8, $this->prodA->fresh()->stock);
        $this->assertEquals(0, $this->prodA->fresh()->reserved_stock);
        $this->assertEquals(4, $this->prodB->fresh()->stock);
        $this->assertEquals(0, $this->prodB->fresh()->reserved_stock);

        // 2. Kiểm tra chỉ tạo đúng 1 Phiếu xuất kho
        $issues = GoodsIssue::where('order_id', $order->id)->get();
        $this->assertCount(1, $issues);
        $issue = $issues->first();
        $this->assertEquals('sale', $issue->type);
        $this->assertEquals('completed', $issue->status);
        $this->assertCount(2, $issue->details);

        // 3. Kiểm tra số lượng xuất kho
        $this->assertEquals(2, $issue->details->where('product_id', $this->prodA->id)->first()->quantity);
        $this->assertEquals(1, $issue->details->where('product_id', $this->prodB->id)->first()->quantity);

        // 4. Kiểm tra Sổ cái InventoryTransaction
        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $this->prodA->id,
            'type' => 'out',
            'quantity' => 2,
            'reference_type' => GoodsIssue::class,
            'reference_id' => $issue->id,
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $this->prodB->id,
            'type' => 'out',
            'quantity' => 1,
            'reference_type' => GoodsIssue::class,
            'reference_id' => $issue->id,
        ]);
    }

    /**
     * Test 10: Chống tạo trùng lặp giao dịch (Idempotency) khi admin bấm nhiều lần hoặc F5.
     */
    public function test_idempotent_order_status_update_does_not_create_duplicate_issue(): void
    {
        $this->prodA->decrement('stock', 1);
        $this->prodA->increment('reserved_stock', 1);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2610140002',
            'recipient_name' => 'Test User',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '123 Đội Cấn',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'delivering',
            'subtotal' => 45000000,
            'shipping_fee' => 0,
            'total' => 45000000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->prodA->id,
            'product_name' => $this->prodA->name,
            'quantity' => 1,
            'unit_price' => $this->prodA->price,
            'subtotal' => 45000000,
        ]);

        // Cập nhật lần 1 sang completed
        $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $order), [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        // Cập nhật lần 2 sang finished (hoặc submit lại form)
        $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $order), [
            'status' => 'finished',
            'payment_status' => 'paid',
        ]);

        // Số phiếu xuất kho vẫn CHÍNH XÁC là 1
        $this->assertEquals(1, GoodsIssue::where('order_id', $order->id)->count());
        // Số transaction 'out' cho phiếu này vẫn CHÍNH XÁC là 1
        $this->assertEquals(1, InventoryTransaction::where('reference_type', GoodsIssue::class)->count());
        // Tồn kho không bị trừ lặp
        $this->assertEquals(9, $this->prodA->fresh()->stock);
        $this->assertEquals(0, $this->prodA->fresh()->reserved_stock);
    }

    /**
     * Test 6: Hủy đơn hàng trước khi xuất kho (giải phóng reserved_stock).
     */
    public function test_cancel_order_before_issue_restores_reserved_stock_to_available(): void
    {
        $this->prodA->decrement('stock', 2);
        $this->prodA->increment('reserved_stock', 2);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2610140003',
            'recipient_name' => 'Test User',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '123 Đội Cấn',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'subtotal' => 90000000,
            'shipping_fee' => 0,
            'total' => 90000000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->prodA->id,
            'product_name' => $this->prodA->name,
            'quantity' => 2,
            'unit_price' => $this->prodA->price,
            'subtotal' => 90000000,
        ]);

        // Hủy đơn
        $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $order), [
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);

        $this->assertEquals(10, $this->prodA->fresh()->stock); // Hoàn lại đầy đủ 10
        $this->assertEquals(0, $this->prodA->fresh()->reserved_stock); // Hết giữ chỗ
        $this->assertEquals(0, GoodsIssue::where('order_id', $order->id)->count()); // Không có phiếu xuất
    }

    /**
     * Test 8 & 9: Hàng trả về chưa xác nhận không cộng kho bán; kiểm định restock thì cộng đúng 1 lần.
     */
    public function test_return_request_and_qc_restock_workflow(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2610140004',
            'recipient_name' => 'Test User',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '123 Đội Cấn',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 45000000,
            'shipping_fee' => 0,
            'total' => 45000000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->prodA->id,
            'product_name' => $this->prodA->name,
            'quantity' => 1,
            'unit_price' => $this->prodA->price,
            'subtotal' => 45000000,
        ]);

        // Khách gửi yêu cầu trả hàng
        $returnReq = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'reason' => 'Không vừa ý',
            'video_url' => 'https://drive.google.com/file/d/test/view',
            'status' => 'pending',
            'bank_name' => 'VCB',
            'bank_account_holder' => 'TEST',
            'bank_account_number' => '123456',
            'refund_status' => 'pending',
            'refund_amount' => 45000000,
            'return_shipping_status' => 'pending_tracking',
        ]);

        // Test 8: Khi yêu cầu đang pending hoặc duyệt -> Tồn kho bán KHÔNG được tự ý tăng
        $stockBefore = $this->prodA->fresh()->stock;
        $this->actingAs($this->admin)->patch(route('admin.returns.update-status', $returnReq), [
            'status' => 'approved',
        ]);
        $this->assertEquals($stockBefore, $this->prodA->fresh()->stock);

        // Test 9: Chỉ khi QC kiểm định đạt chuẩn (restock) -> Tồn kho bán mới tăng đúng 1 lần
        $this->actingAs($this->admin)->post(route('admin.qc_inspections.store'), [
            'return_request_id' => $returnReq->id,
            'product_id' => $this->prodA->id,
            'condition' => 'perfect',
            'final_action' => 'restock',
            'quantity' => 1,
            'inspection_note' => 'Hàng mới 100%, nguyên seal, nhập lại kho',
        ]);

        $this->assertEquals($stockBefore + 1, $this->prodA->fresh()->stock);
        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $this->prodA->id,
            'type' => 'in',
            'quantity' => 1,
            'reference_type' => QcInspection::class,
        ]);
    }
}
