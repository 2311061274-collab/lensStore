<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductImageSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    private array $filesToClean = [];

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'manage_products']);
        Permission::firstOrCreate(['name' => 'manage_categories']);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->givePermissionTo(['manage_products', 'manage_categories']);

        $this->category = Category::create([
            'name' => 'Ống kính Sony',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->filesToClean as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        parent::tearDown();
    }

    public function test_admin_can_create_product_with_uploaded_image_and_syncs_to_storefront(): void
    {
        $file = UploadedFile::fake()->image('sony_lens_2470.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Sony FE 24-70mm f/2.8 GM II',
            'sku' => 'SEL2470GM2',
            'category_id' => $this->category->id,
            'price' => 49990000,
            'stock' => 5,
            'status' => 'in_stock',
            'image_file' => $file,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'SEL2470GM2')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image);
        $this->assertStringStartsWith('uploads/products/', $product->image);

        $savedFilePath = public_path($product->image);
        $this->filesToClean[] = $savedFilePath;
        $this->assertTrue(File::exists($savedFilePath));

        // Kiểm tra Accessor
        $this->assertEquals(asset($product->image), $product->image_url);

        // Kiểm tra đồng bộ trên Storefront: Chi tiết sản phẩm & Trang danh sách sản phẩm
        $showResponse = $this->get(route('storefront.show', $product->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($product->image_url, false);

        $productsResponse = $this->get(route('storefront.products'));
        $productsResponse->assertStatus(200);
        $productsResponse->assertSee($product->image_url, false);
    }

    public function test_admin_updates_product_with_new_image_and_cleans_old_image(): void
    {
        // 1. Tạo sản phẩm ban đầu với ảnh 1
        $file1 = UploadedFile::fake()->image('old_image.jpg', 600, 600);
        $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Sigma 85mm f/1.4 DG DN',
            'sku' => 'SIGMA85',
            'category_id' => $this->category->id,
            'price' => 25000000,
            'stock' => 3,
            'status' => 'in_stock',
            'image_file' => $file1,
        ]);

        $product = Product::where('sku', 'SIGMA85')->first();
        $oldImagePath = public_path($product->image);
        $this->filesToClean[] = $oldImagePath;
        $this->assertTrue(File::exists($oldImagePath));

        // 2. Cập nhật với ảnh 2
        $file2 = UploadedFile::fake()->image('new_image.jpg', 800, 800);
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Sigma 85mm f/1.4 DG DN Art Updated',
            'sku' => 'SIGMA85',
            'category_id' => $this->category->id,
            'price' => 26000000,
            'stock' => 4,
            'status' => 'in_stock',
            'image_file' => $file2,
        ]);

        $updateResponse->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $newImagePath = public_path($product->image);
        $this->filesToClean[] = $newImagePath;

        $this->assertNotEquals($oldImagePath, $newImagePath);
        $this->assertTrue(File::exists($newImagePath));
        // File cũ phải được dọn dẹp an toàn
        $this->assertFalse(File::exists($oldImagePath));

        // Trang storefront phải hiển thị ảnh mới
        $showResponse = $this->get(route('storefront.show', $product->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($product->image_url, false);
    }

    public function test_admin_updates_product_without_new_image_preserves_existing_image(): void
    {
        $file = UploadedFile::fake()->image('preserve_me.jpg', 600, 600);
        $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Canon RF 50mm f/1.2L',
            'sku' => 'RF50F12',
            'category_id' => $this->category->id,
            'price' => 52000000,
            'stock' => 2,
            'status' => 'in_stock',
            'image_file' => $file,
        ]);

        $product = Product::where('sku', 'RF50F12')->first();
        $originalImagePath = $product->image;
        $savedFilePath = public_path($originalImagePath);
        $this->filesToClean[] = $savedFilePath;
        $this->assertTrue(File::exists($savedFilePath));

        // Cập nhật giá và tên, KHÔNG gửi image_file
        $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Canon RF 50mm f/1.2L USM Red Ring',
            'sku' => 'RF50F12',
            'category_id' => $this->category->id,
            'price' => 51000000,
            'stock' => 2,
            'status' => 'in_stock',
        ]);

        $product->refresh();
        $this->assertEquals($originalImagePath, $product->image);
        $this->assertTrue(File::exists($savedFilePath));
    }

    public function test_validation_rejects_invalid_image_file(): void
    {
        $invalidFile = UploadedFile::fake()->create('malicious.exe', 500);

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Tamron 28-75mm G2',
            'sku' => 'TAM2875G2',
            'category_id' => $this->category->id,
            'price' => 19000000,
            'stock' => 10,
            'image_file' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['image_file']);
        $this->assertDatabaseMissing('products', ['sku' => 'TAM2875G2']);
    }

    public function test_non_admin_cannot_upload_or_modify_product_image(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->post(route('admin.products.store'), [
            'name' => 'Unauthorized Lens',
            'sku' => 'UNAUTH01',
            'category_id' => $this->category->id,
            'price' => 1000000,
            'stock' => 1,
        ]);

        $response->assertStatus(403);
    }
}
