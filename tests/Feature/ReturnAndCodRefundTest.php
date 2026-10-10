<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReturnAndCodRefundTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $admin;

    private Order $completedOrder;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'manage_orders']);

        $this->customer = User::factory()->create(['role' => 'customer']);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->givePermissionTo('manage_orders');

        $this->completedOrder = Order::create([
            'order_code' => 'ORD-RETURN-001',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Mua Lens',
            'recipient_phone' => '0912345678',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '456 Hoàng Hoa Thám',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed', // Delivered order eligible for return
            'subtotal' => 15000000,
            'shipping_fee' => 30000,
            'total' => 15030000,
        ]);
    }

    public function test_customer_can_create_return_request_with_bank_details(): void
    {
        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Sản phẩm lỗi kỹ thuật',
            'note' => 'Vòng zoom bị kẹt khi xoay',
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
        ]);

        $response->assertRedirect(route('orders.show', $this->completedOrder));

        $this->assertDatabaseHas('return_requests', [
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Sản phẩm lỗi kỹ thuật',
            'status' => 'pending',
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
        ]);

        $this->assertEquals('returning', $this->completedOrder->fresh()->status);
    }

    public function test_customer_cannot_return_order_of_another_user(): void
    {
        $anotherCustomer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($anotherCustomer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Lý do giả mạo',
            'bank_name' => 'MB Bank',
            'bank_account_holder' => 'KE GIA MAO',
            'bank_account_number' => '999888777',
        ]);

        $response->assertStatus(403);
    }

    public function test_cannot_return_pending_or_undelivered_order(): void
    {
        $pendingOrder = Order::create([
            'order_code' => 'ORD-PENDING-002',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Mua Lens',
            'recipient_phone' => '0912345678',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '456 Hoàng Hoa Thám',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'pending', // NOT yet completed/delivered
            'subtotal' => 10000000,
            'shipping_fee' => 0,
            'total' => 10000000,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $pendingOrder), [
            'reason' => 'Muốn hủy đổi trả',
            'bank_name' => 'ACB',
            'bank_account_holder' => 'KHACH HANG',
            'bank_account_number' => '12345678',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('return_requests', ['order_id' => $pendingOrder->id]);
    }

    public function test_admin_can_approve_return_request_without_prematurely_marking_refunded(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Sản phẩm lỗi',
            'status' => 'pending',
            'bank_name' => 'Techcombank',
            'bank_account_holder' => 'KHACH TEST',
            'bank_account_number' => '190333222111',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.returns.update-status', $returnRequest), [
            'status' => 'approved',
        ]);

        $response->assertSessionHas('success');
        $fresh = $returnRequest->fresh();

        $this->assertEquals('approved', $fresh->status);
        // Master Prompt: Approving return does NOT automatically mean money is refunded yet!
        $this->assertEquals('pending', $fresh->refund_status);
        $this->assertNotEmpty($fresh->tracking_code);
    }

    public function test_admin_can_confirm_cod_refund_with_audit_trail(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Sản phẩm lỗi',
            'status' => 'approved', // Must be approved first
            'bank_name' => 'VietinBank',
            'bank_account_holder' => 'NGUYEN NHAN TIEN',
            'bank_account_number' => '1020304050',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.returns.confirm-refund', $returnRequest), [
            'refund_amount' => 15030000,
            'refund_method' => 'bank_transfer',
            'refund_reference' => 'FT261010998877',
            'refund_note' => 'Đã chuyển khoản hoàn tất từ tài khoản công ty',
        ]);

        $response->assertSessionHas('success');
        $fresh = $returnRequest->fresh();

        $this->assertEquals('refunded', $fresh->refund_status);
        $this->assertEquals($this->admin->id, $fresh->refunded_by);
        $this->assertNotNull($fresh->refunded_at);
        $this->assertEquals('FT261010998877', $fresh->refund_reference);
    }

    public function test_idempotent_cannot_confirm_refund_twice(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Sản phẩm lỗi',
            'status' => 'approved',
            'bank_name' => 'VietinBank',
            'bank_account_holder' => 'NGUYEN NHAN TIEN',
            'bank_account_number' => '1020304050',
            'refund_status' => 'refunded', // Already refunded
            'refund_amount' => 15030000,
            'refunded_at' => now(),
            'refunded_by' => $this->admin->id,
        ]);

        // Second attempt to refund must be rejected
        $response = $this->actingAs($this->admin)->patch(route('admin.returns.confirm-refund', $returnRequest), [
            'refund_method' => 'bank_transfer',
            'refund_reference' => 'DUPLICATE_ATTEMPT',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_customer_can_upload_valid_evidence_image_on_return_request(): void
    {
        $fakeImage = UploadedFile::fake()->image('broken_lens.jpg', 640, 480)->size(1024);

        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Hàng vỡ hỏng trong quá trình vận chuyển',
            'note' => 'Kính trước có vết nứt dài',
            'image' => $fakeImage,
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
        ]);

        $response->assertRedirect(route('orders.show', $this->completedOrder));

        $returnReq = ReturnRequest::where('order_id', $this->completedOrder->id)->first();
        $this->assertNotNull($returnReq);
        $this->assertNotNull($returnReq->image);
        $this->assertStringStartsWith('uploads/returns/', $returnReq->image);
        $this->assertFileExists(public_path($returnReq->image));
        $this->assertNotNull($returnReq->image_url);

        // Dọn dẹp file test
        if (File::exists(public_path($returnReq->image))) {
            File::delete(public_path($returnReq->image));
        }
    }

    public function test_backend_rejects_non_image_evidence_file(): void
    {
        $fakeScript = UploadedFile::fake()->create('malicious.php', 10, 'text/x-php');

        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Thử nghiệm tải tệp độc hại',
            'image' => $fakeScript,
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
        ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('return_requests', ['order_id' => $this->completedOrder->id]);
    }

    public function test_backend_rejects_oversized_evidence_image(): void
    {
        // 6000 KB > 5120 KB limit
        $oversizedImage = UploadedFile::fake()->image('huge.jpg')->size(6000);

        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Ảnh quá dung lượng 5MB',
            'image' => $oversizedImage,
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
        ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('return_requests', ['order_id' => $this->completedOrder->id]);
    }

    public function test_admin_can_view_return_request_with_evidence_image(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Thấu kính bị trầy xước nặng',
            'note' => 'Bằng chứng kèm theo',
            'image' => 'uploads/returns/sample_evidence.jpg',
            'status' => 'pending',
            'bank_name' => 'ACB',
            'bank_account_holder' => 'NGUYEN TEST',
            'bank_account_number' => '123456789',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.returns.index'));
        $response->assertStatus(200);
        $response->assertSee('Thấu kính bị trầy xước nặng');
        $response->assertSee('Xem ảnh');
    }
}
