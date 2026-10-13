<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_customer_can_create_return_request_with_google_drive_video_and_bank_details(): void
    {
        $driveLink = 'https://drive.google.com/file/d/1B2C3D4E5F6G7H8I9J0K/view?usp=sharing';

        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Sản phẩm lỗi kỹ thuật',
            'note' => 'Vòng zoom bị kẹt khi xoay',
            'video_url' => $driveLink,
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
            'video_url' => $driveLink,
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
            'return_shipping_status' => 'pending_tracking',
        ]);

        $returnReq = ReturnRequest::where('order_id', $this->completedOrder->id)->first();
        $this->assertNotNull($returnReq);
        $this->assertNotNull($returnReq->request_code);
        $this->assertStringStartsWith('RT-', $returnReq->request_code);

        $this->assertEquals('returning', $this->completedOrder->fresh()->status);
    }

    public function test_customer_cannot_return_order_of_another_user(): void
    {
        $anotherCustomer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($anotherCustomer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Cố tình trả đơn của người khác',
            'video_url' => 'https://drive.google.com/file/d/12345/view',
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'HACKER',
            'bank_account_number' => '9999999999',
        ]);

        $response->assertStatus(403);
    }

    public function test_cannot_return_pending_or_undelivered_order(): void
    {
        $pendingOrder = Order::create([
            'order_code' => 'ORD-PENDING-001',
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
            'status' => 'pending', // Not completed yet
            'subtotal' => 5000000,
            'shipping_fee' => 30000,
            'total' => 5030000,
        ]);

        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $pendingOrder), [
            'reason' => 'Đổi ý không mua nữa',
            'video_url' => 'https://drive.google.com/file/d/12345/view',
            'bank_name' => 'Techcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '190300112233',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('return_requests', ['order_id' => $pendingOrder->id]);
    }

    public function test_backend_rejects_non_google_drive_video_url(): void
    {
        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Sản phẩm lỗi kỹ thuật',
            'video_url' => 'https://youtube.com/watch?v=12345678',
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
        ]);

        $response->assertSessionHasErrors(['video_url']);
        $this->assertDatabaseMissing('return_requests', ['order_id' => $this->completedOrder->id]);
    }

    public function test_backend_rejects_empty_video_url(): void
    {
        $response = $this->actingAs($this->customer)->post(route('orders.return.store', $this->completedOrder), [
            'reason' => 'Sản phẩm lỗi kỹ thuật',
            'video_url' => '',
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
        ]);

        $response->assertSessionHasErrors(['video_url']);
        $this->assertDatabaseMissing('return_requests', ['order_id' => $this->completedOrder->id]);
    }

    public function test_admin_can_approve_return_request_without_prematurely_marking_refunded(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Sản phẩm lỗi kỹ thuật',
            'video_url' => 'https://drive.google.com/file/d/abcxyz/view',
            'status' => 'pending',
            'bank_name' => 'Vietcombank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '0011002233445',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
            'return_shipping_status' => 'pending_tracking',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.returns.update-status', $returnRequest), [
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('return_requests', [
            'id' => $returnRequest->id,
            'status' => 'approved',
            'refund_status' => 'pending', // NOT prematurely refunded!
            'return_shipping_status' => 'pending_tracking',
        ]);

        $this->assertEquals('returning', $this->completedOrder->fresh()->status);
    }

    public function test_admin_can_update_waybill_tracking_code_and_shipping_status(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Hàng không đúng mô tả',
            'video_url' => 'https://drive.google.com/file/d/abcxyz/view',
            'status' => 'approved',
            'bank_name' => 'MB Bank',
            'bank_account_holder' => 'NGUYEN VAN KHACH',
            'bank_account_number' => '123456789',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
            'return_shipping_status' => 'pending_tracking',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.returns.update-tracking', $returnRequest), [
            'tracking_code' => 'GHN-RETURN-998877',
            'return_shipping_status' => 'picking_up',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('return_requests', [
            'id' => $returnRequest->id,
            'tracking_code' => 'GHN-RETURN-998877',
            'return_shipping_status' => 'picking_up',
        ]);
    }

    public function test_admin_can_confirm_cod_refund_with_audit_trail(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Sản phẩm lỗi',
            'video_url' => 'https://drive.google.com/file/d/test1234/view',
            'status' => 'approved',
            'bank_name' => 'VietinBank',
            'bank_account_holder' => 'NGUYEN NHAN TIEN',
            'bank_account_number' => '1020304050',
            'refund_status' => 'pending',
            'refund_amount' => 15030000,
            'return_shipping_status' => 'received',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.returns.confirm-refund', $returnRequest), [
            'refund_amount' => 15030000,
            'refund_method' => 'bank_transfer',
            'refund_reference' => 'FT260929987654321',
            'refund_note' => 'Đã chuyển khoản qua Internet Banking VCB',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('return_requests', [
            'id' => $returnRequest->id,
            'refund_status' => 'refunded',
            'refund_method' => 'bank_transfer',
            'refund_reference' => 'FT260929987654321',
            'refunded_by' => $this->admin->id,
        ]);

        $this->assertEquals('returned', $this->completedOrder->fresh()->status);
    }

    public function test_idempotent_cannot_confirm_refund_twice(): void
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'reason' => 'Sản phẩm lỗi',
            'video_url' => 'https://drive.google.com/file/d/test1234/view',
            'status' => 'approved',
            'bank_name' => 'VietinBank',
            'bank_account_holder' => 'NGUYEN NHAN TIEN',
            'bank_account_number' => '1020304050',
            'refund_status' => 'refunded', // Already refunded
            'refund_amount' => 15030000,
            'refunded_at' => now(),
            'refunded_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.returns.confirm-refund', $returnRequest), [
            'refund_amount' => 15030000,
            'refund_method' => 'bank_transfer',
            'refund_reference' => 'DUPLICATE_ATTEMPT',
        ]);

        $response->assertSessionHas('error');
    }
}
