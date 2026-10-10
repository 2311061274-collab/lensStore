<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DashboardRealtimeAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_dashboard']);
        Permission::firstOrCreate(['name' => 'manage_orders']);
        Permission::firstOrCreate(['name' => 'manage_products']);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->givePermissionTo(['view_dashboard', 'manage_orders', 'manage_products']);

        $this->customer = User::factory()->create(['role' => 'customer']);

        $this->category = Category::create([
            'name' => 'Ống kính Sony E-Mount',
            'is_active' => true,
        ]);
    }

    public function test_label_45s_is_removed_and_manual_refresh_is_present(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Master Prompt Section 7: Không hiển thị nhãn "Tự làm mới (45s)"
        $response->assertDontSee('Tự làm mới (45s)');
        // Nút "Làm mới" thủ công vẫn tồn tại
        $response->assertSee('Làm mới');
    }

    public function test_api_dashboard_data_returns_accurate_realtime_metrics(): void
    {
        // 1. Tạo đơn hoàn tất có thanh toán
        Order::create([
            'order_code' => 'ORD-API-001',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách A',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '123 Phố Huế',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 10000000,
            'shipping_fee' => 0,
            'total' => 10000000,
        ]);

        // 2. Tạo đơn COD chưa thu
        Order::create([
            'order_code' => 'ORD-API-002',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách B',
            'recipient_phone' => '0901234568',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '456 Phố Huế',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'delivering',
            'subtotal' => 5000000,
            'shipping_fee' => 30000,
            'total' => 5030000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'timestamp',
            'lastUpdated',
            'counts' => [
                'ordersToday',
                'completedOrders',
                'pendingCodAmount',
                'revenueToday',
                'outOfStockCount',
                'lowStockCount',
            ],
            'formatted',
            'charts' => ['labels', 'values', 'brandPie'],
            'lowStock',
            'stuckShipping',
        ]);

        $data = $response->json();
        $this->assertEquals(2, $data['counts']['ordersToday']);
        $this->assertEquals(1, $data['counts']['completedOrders']);
        $this->assertEquals(5030000, $data['counts']['pendingCodAmount']);
        $this->assertEquals(10000000, $data['counts']['revenueToday']);
    }

    public function test_case_a_completed_orders_count_and_navigation_filter(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-COMPLETE-01',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Hoàn Tất',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => 'Số 1 Kim Mã',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 12000000,
            'shipping_fee' => 0,
            'total' => 12000000,
        ]);

        // Kiểm tra dashboard đếm đúng 1 đơn hoàn tất
        $dashResponse = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(1, $dashResponse->json('counts.completedOrders'));

        // Kiểm tra điều hướng đến trang danh sách đơn lọc đúng đơn hoàn tất
        $filterResponse = $this->actingAs($this->admin)->get(route('admin.orders.index', ['status' => 'completed']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('ORD-COMPLETE-01');
    }

    public function test_case_b_new_order_and_pending_cancellation(): void
    {
        // Tạo đơn hàng mới trạng thái pending
        $order = Order::create([
            'order_code' => 'ORD-NEW-PENDING',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Mới Chờ Duyệt',
            'recipient_phone' => '0909998887',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => 'Số 10 Tràng Thi',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'subtotal' => 7000000,
            'shipping_fee' => 0,
            'total' => 7000000,
        ]);

        $dashResponse = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(1, $dashResponse->json('counts.pendingOrders'));
        $this->assertEquals(1, $dashResponse->json('counts.ordersToday'));

        // Điều hướng lọc theo ngày hôm nay
        $dateFilter = $this->actingAs($this->admin)->get(route('admin.orders.index', ['date' => 'today']));
        $dateFilter->assertStatus(200);
        $dateFilter->assertSee('ORD-NEW-PENDING');

        // Hủy đơn hàng -> số đơn pending phải giảm về 0
        $order->update(['status' => 'cancelled']);

        $dashAfterCancel = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(0, $dashAfterCancel->json('counts.pendingOrders'));
        $this->assertEquals(1, $dashAfterCancel->json('counts.cancelledOrders'));
    }

    public function test_case_c_and_d_stock_status_out_of_stock_and_low_stock(): void
    {
        // 1. Sản phẩm hết hàng (stock = 0)
        $p1 = Product::create([
            'name' => 'Sony FE 50mm f/1.2 GM Hết Hàng',
            'sku' => 'SEL50F12GM-0',
            'price' => 45000000,
            'stock' => 0,
            'category_id' => $this->category->id,
        ]);

        // 2. Sản phẩm sắp hết hàng (stock = 2)
        $p2 = Product::create([
            'name' => 'Sony FE 35mm f/1.4 GM Sắp Hết',
            'sku' => 'SEL35F14GM-2',
            'price' => 32000000,
            'stock' => 2,
            'category_id' => $this->category->id,
        ]);

        // 3. Sản phẩm dồi dào hàng (stock = 10)
        $p3 = Product::create([
            'name' => 'Sony FE 24-70mm f/2.8 Đủ Hàng',
            'sku' => 'SEL2470GM-10',
            'price' => 40000000,
            'stock' => 10,
            'category_id' => $this->category->id,
        ]);

        $dashResponse = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(1, $dashResponse->json('counts.outOfStockCount'));
        $this->assertEquals(1, $dashResponse->json('counts.lowStockCount'));

        // Kiểm tra danh sách lowStock trên API
        $lowStockList = $dashResponse->json('lowStock');
        $this->assertCount(2, $lowStockList); // Chỉ gồm p1 và p2, không chứa p3

        // Kiểm tra bộ lọc sản phẩm: stock_status=out_of_stock
        $filterOut = $this->actingAs($this->admin)->get(route('admin.products.index', ['stock_status' => 'out_of_stock']));
        $filterOut->assertSee('SEL50F12GM-0');
        $filterOut->assertDontSee('SEL2470GM-10');

        // Kiểm tra bộ lọc sản phẩm: stock_status=low
        $filterLow = $this->actingAs($this->admin)->get(route('admin.products.index', ['stock_status' => 'low']));
        $filterLow->assertSee('SEL35F14GM-2');
        $filterLow->assertDontSee('SEL2470GM-10');

        // Cập nhật p1 nhập thêm hàng lên tồn 5 -> không còn hết hàng
        $p1->update(['stock' => 5]);
        $dashAfterRestock = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(0, $dashAfterRestock->json('counts.outOfStockCount'));
    }

    public function test_case_e_delayed_shipping_orders(): void
    {
        // 1. Đơn đang giao trễ (> 3 ngày)
        $delayedOrder = Order::create([
            'order_code' => 'ORD-DELAYED-01',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Bị Trễ Đơn',
            'recipient_phone' => '0912999888',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => 'Số 99 Nguyễn Chí Thanh',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'delivering',
            'subtotal' => 15000000,
            'shipping_fee' => 0,
            'total' => 15000000,
        ]);
        Order::where('id', $delayedOrder->id)->update(['updated_at' => now()->subDays(5)]);

        // 2. Đơn đã giao thành công (completed) dù cập nhật 5 ngày trước cũng không tính là trễ
        $completedOrder = Order::create([
            'order_code' => 'ORD-COMPLETED-SAFE',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Đã Nhận',
            'recipient_phone' => '0912111222',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => 'Số 88 Láng Hạ',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 10000000,
            'shipping_fee' => 0,
            'total' => 10000000,
        ]);
        Order::where('id', $completedOrder->id)->update(['updated_at' => now()->subDays(5)]);

        $dashResponse = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(1, $dashResponse->json('counts.stuckShippingCount'));
        $stuckList = $dashResponse->json('stuckShipping');
        $this->assertEquals('ORD-DELAYED-01', $stuckList[0]['order_code']);
        $this->assertEquals(2, $stuckList[0]['days_delayed']); // 5 - 3 = 2 ngày trễ

        // Kiểm tra điều hướng: shipping_delayed=1
        $delayedFilter = $this->actingAs($this->admin)->get(route('admin.orders.index', ['shipping_delayed' => 1]));
        $delayedFilter->assertSee('ORD-DELAYED-01');
        $delayedFilter->assertDontSee('ORD-COMPLETED-SAFE');
    }

    public function test_case_f_returns_and_refunds_dynamic_lifecycle(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-RETURN-LIFE',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Hoàn Hàng',
            'recipient_phone' => '0912888777',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => 'Số 1 Liễu Giai',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 20000000,
            'shipping_fee' => 0,
            'total' => 20000000,
        ]);

        // 1. Tạo yêu cầu đổi trả mới
        $rr = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'reason' => 'Lỗi thấu kính',
            'status' => 'pending',
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'KHACH HOAN HANG',
            'bank_account_number' => '0011223344',
            'refund_status' => 'pending',
            'refund_amount' => 20000000,
        ]);

        $dash1 = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(1, $dash1->json('counts.pendingReturns'));
        $this->assertEquals(0, $dash1->json('counts.pendingRefunds'));

        // 2. Admin duyệt yêu cầu
        $rr->update(['status' => 'approved']);
        $dash2 = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(0, $dash2->json('counts.pendingReturns'));
        $this->assertEquals(1, $dash2->json('counts.pendingRefunds'));

        // 3. Admin xác nhận hoàn tiền
        $rr->update([
            'refund_status' => 'refunded',
            'refunded_at' => now(),
            'refunded_by' => $this->admin->id,
        ]);

        $dash3 = $this->actingAs($this->admin)->get(route('admin.dashboard.data'));
        $this->assertEquals(0, $dash3->json('counts.pendingRefunds'));
        $this->assertEquals(20000000, $dash3->json('counts.totalRefunded'));
    }
}
