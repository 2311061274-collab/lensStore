<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $otherCustomer;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->otherCustomer = User::factory()->create(['role' => 'customer']);

        $category = Category::create([
            'name' => 'Ống kính Canon',
            'slug' => 'ong-kinh-canon',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Canon RF 50mm f/1.2L USM',
            'brand' => 'Canon',
            'price' => 52000000,
            'stock' => 5,
            'reserved_stock' => 2,
            'defective_stock' => 0,
            'status' => 'in_stock',
        ]);
    }

    public function test_customer_can_cancel_pending_order_and_restore_inventory(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2601010001',
            'recipient_name' => 'Nguyễn Văn A',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1482,
            'district_name' => 'Ba Đình',
            'ward_code' => '11001',
            'ward_name' => 'Điện Biên',
            'address_detail' => '12 Kim Mã',
            'shipping_fee' => 30000,
            'subtotal' => 104000000,
            'total' => 104030000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'unit_price' => $this->product->price,
            'quantity' => 2,
            'subtotal' => 104000000,
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('orders.cancel', $order->id));

        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        $this->product->refresh();
        $this->assertEquals(7, $this->product->stock); // 5 + 2
        $this->assertEquals(0, $this->product->reserved_stock); // 2 - 2

        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $this->product->id,
            'reference_type' => Order::class,
            'reference_id' => $order->id,
            'type' => 'adjustment',
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_cancel_another_users_order(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2601010002',
            'recipient_name' => 'Nguyễn Văn A',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1482,
            'district_name' => 'Ba Đình',
            'ward_code' => '11001',
            'ward_name' => 'Điện Biên',
            'address_detail' => '12 Kim Mã',
            'shipping_fee' => 30000,
            'subtotal' => 52000000,
            'total' => 52030000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->otherCustomer)
            ->post(route('orders.cancel', $order->id));

        $response->assertStatus(403);
    }

    public function test_customer_cannot_cancel_completed_order(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2601010003',
            'recipient_name' => 'Nguyễn Văn A',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1482,
            'district_name' => 'Ba Đình',
            'ward_code' => '11001',
            'ward_name' => 'Điện Biên',
            'address_detail' => '12 Kim Mã',
            'shipping_fee' => 30000,
            'subtotal' => 52000000,
            'total' => 52030000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('orders.cancel', $order->id));

        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals('completed', $order->status);
    }

    public function test_orders_page_displays_cancel_button_for_pending_orders(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'LS2601010004',
            'recipient_name' => 'Nguyễn Văn A',
            'recipient_phone' => '0901234567',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1482,
            'district_name' => 'Ba Đình',
            'ward_code' => '11001',
            'ward_name' => 'Điện Biên',
            'address_detail' => '12 Kim Mã',
            'shipping_fee' => 30000,
            'subtotal' => 52000000,
            'total' => 52030000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Hủy đơn hàng');
        $response->assertSee(route('orders.cancel', $order->id));
    }
}
