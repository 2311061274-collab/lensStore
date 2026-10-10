<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductAndInventorySafetyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::firstOrCreate(['name' => 'manage_products']);
        Permission::firstOrCreate(['name' => 'manage_categories']);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->admin->givePermissionTo(['manage_products', 'manage_categories']);

        $this->category = Category::create([
            'name' => 'Ống kính Sony E-Mount',
            'description' => 'Ống kính dành cho máy Sony',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_admin_products(): void
    {
        $response = $this->get(route('admin.products.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_cannot_create_product_with_duplicate_sku(): void
    {
        Product::create([
            'name' => 'Sony FE 50mm f/1.8',
            'sku' => 'SEL50F18F',
            'price' => 5000000,
            'stock' => 10,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Sony FE 50mm Duplicate SKU',
            'sku' => 'SEL50F18F', // Duplicate SKU
            'price' => 5500000,
            'category_id' => $this->category->id,
            'stock' => 5,
        ]);

        $response->assertSessionHasErrors(['sku']);
    }

    public function test_product_update_rejects_duplicate_sku_from_another_product_but_accepts_own_sku(): void
    {
        $p1 = Product::create([
            'name' => 'Sony FE 24-70mm f/2.8 GM',
            'sku' => 'SEL2470GM',
            'price' => 40000000,
            'stock' => 5,
            'category_id' => $this->category->id,
        ]);

        $p2 = Product::create([
            'name' => 'Sony FE 70-200mm f/2.8 GM',
            'sku' => 'SEL70200GM',
            'price' => 50000000,
            'stock' => 3,
            'category_id' => $this->category->id,
        ]);

        // Trying to update p2 with p1's SKU should fail
        $responseDuplicate = $this->actingAs($this->admin)->put(route('admin.products.update', $p2), [
            'name' => 'Sony FE 70-200mm Updated',
            'sku' => 'SEL2470GM',
            'price' => 50000000,
            'category_id' => $this->category->id,
            'status' => 'in_stock',
        ]);
        $responseDuplicate->assertSessionHasErrors(['sku']);

        // Updating p2 while keeping its own SKU should succeed
        $responseOwn = $this->actingAs($this->admin)->put(route('admin.products.update', $p2), [
            'name' => 'Sony FE 70-200mm GM Mark I',
            'sku' => 'SEL70200GM',
            'price' => 48000000,
            'category_id' => $this->category->id,
            'status' => 'in_stock',
        ]);
        $responseOwn->assertSessionHasNoErrors();
        $this->assertEquals('Sony FE 70-200mm GM Mark I', $p2->fresh()->name);
    }

    public function test_product_update_does_not_modify_stock_directly(): void
    {
        $product = Product::create([
            'name' => 'Sony FE 85mm f/1.8',
            'sku' => 'SEL85F18',
            'price' => 12000000,
            'stock' => 15, // Original stock
            'category_id' => $this->category->id,
        ]);

        // Request attempting to change stock to 999 directly
        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), [
            'name' => 'Sony FE 85mm f/1.8 Renamed',
            'sku' => 'SEL85F18',
            'price' => 12500000,
            'category_id' => $this->category->id,
            'status' => 'in_stock',
            'stock' => 999, // Should be ignored according to Master Prompt rules
        ]);

        $response->assertSessionHasNoErrors();
        // Verify stock remains exactly 15
        $this->assertEquals(15, $product->fresh()->stock);
    }

    public function test_cannot_delete_product_with_order_history(): void
    {
        $product = Product::create([
            'name' => 'Sony FE 35mm f/1.4 GM',
            'sku' => 'SEL35F14GM',
            'price' => 32000000,
            'stock' => 8,
            'category_id' => $this->category->id,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'order_code' => 'ORD-SAFETY-001',
            'user_id' => $customer->id,
            'recipient_name' => 'Nguyễn Mua Hàng',
            'recipient_phone' => '0988776655',
            'province_id' => 201,
            'province_name' => 'Hà Nội',
            'district_id' => 1484,
            'district_name' => 'Ba Đình',
            'ward_code' => '1A0101',
            'ward_name' => 'Phúc Xá',
            'address_detail' => '123 Phố Huế, Hà Nội',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'pending',
            'subtotal' => 32000000,
            'shipping_fee' => 0,
            'total' => 32000000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        // Attempting to delete product with order history must be blocked
        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_cannot_delete_category_with_existing_products(): void
    {
        Product::create([
            'name' => 'Sony FE 20mm f/1.8 G',
            'sku' => 'SEL20F18G',
            'price' => 18000000,
            'stock' => 4,
            'category_id' => $this->category->id,
        ]);

        // Attempting to delete category that still has products
        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $this->category));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }

    public function test_product_image_url_validation_rejects_unsafe_schemes(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Sony Dangerous Image',
            'sku' => 'DANGER01',
            'price' => 1000000,
            'category_id' => $this->category->id,
            'image_url' => 'javascript:alert(1)', // Unsafe scheme
        ]);

        $response->assertSessionHasErrors(['image_url']);
    }
}
