<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_dashboard']);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->givePermissionTo('view_dashboard');

        $this->customer = User::factory()->create(['role' => 'customer']);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_revenue_only_counts_paid_orders_and_excludes_unpaid_cod(): void
    {
        // 1. Paid order (should count towards revenue)
        Order::create([
            'order_code' => 'ORD-PAID-001',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Paid',
            'recipient_phone' => '0912345678',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '123 Phố Huế',
            'payment_method' => 'momo',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 20000000,
            'shipping_fee' => 0,
            'total' => 20000000,
        ]);

        // 2. Unpaid COD order in transit (must NOT count towards recognized revenue!)
        Order::create([
            'order_code' => 'ORD-COD-PENDING-002',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách COD Chưa Thu',
            'recipient_phone' => '0912345679',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '456 Phố Huế',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'shipping',
            'subtotal' => 35000000,
            'shipping_fee' => 50000,
            'total' => 35050000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('revenueToday', 20000000);
        $response->assertViewHas('pendingCodAmount', 35050000);
    }

    public function test_pending_returns_and_refunds_metrics(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-RETURN-METRICS',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Đổi Trả',
            'recipient_phone' => '0912345678',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '789 Láng Hạ',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 10000000,
            'shipping_fee' => 0,
            'total' => 10000000,
        ]);

        ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'reason' => 'Thử nghiệm metrics',
            'status' => 'pending',
            'bank_name' => 'MB',
            'bank_account_holder' => 'TEST',
            'bank_account_number' => '123456',
            'refund_status' => 'pending',
            'refund_amount' => 10000000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('pendingReturns', 1);
        $response->assertViewHas('pendingRefunds', 0); // Not approved yet, so pendingRefunds is 0
    }
}
