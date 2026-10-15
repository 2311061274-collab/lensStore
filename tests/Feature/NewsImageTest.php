<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_image_accessor_falls_back_to_local_project_asset(): void
    {
        $news = News::create([
            'title' => 'Canon RF 50mm f/1.2L USM — Đỉnh cao bokeh',
            'description' => 'Mô tả ngắn',
            'content' => 'Nội dung bài viết',
            'image_url' => '/storage/news/non_existent_image.webp',
            'tag' => 'review',
            'tag_text' => 'Review',
            'author' => 'Admin',
            'read_time' => 5,
        ]);

        $this->assertStringContainsString('uploads/products/1790239840_6ab4e4606a454.jpg', $news->image_url);
    }

    public function test_news_page_loads_successfully_with_project_images(): void
    {
        News::create([
            'title' => 'Sony FE 85mm f/1.4 GM II — Ống kính chân dung',
            'description' => 'Mô tả bài viết Sony',
            'content' => 'Nội dung chi tiết',
            'image_url' => '/uploads/products/1790238927_6ab4e0cfb31a4.jpg',
            'tag' => 'review',
            'tag_text' => 'Review',
            'author' => 'Admin',
            'read_time' => 7,
            'is_featured' => true,
        ]);

        $response = $this->get(route('storefront.news'));

        $response->assertStatus(200);
        $response->assertSee('uploads/products/1790238927_6ab4e0cfb31a4.jpg');
        $response->assertSee('onerror="this.onerror=null;', false);
    }

    public function test_news_ajax_endpoint_returns_valid_images(): void
    {
        News::create([
            'title' => '7 kỹ thuật bố cục ảnh đẹp',
            'description' => 'Mô tả bố cục',
            'content' => 'Nội dung bố cục',
            'image_url' => '/uploads/products/1790239745_6ab4e4017e75b.jpg',
            'tag' => 'tips',
            'tag_text' => 'Mẹo',
            'author' => 'Admin',
            'read_time' => 6,
        ]);

        $response = $this->getJson(route('storefront.news.ajax'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => '7 kỹ thuật bố cục ảnh đẹp',
        ]);
        $data = $response->json('articles');
        $this->assertNotEmpty($data);
        $this->assertStringContainsString('uploads/products/', $data[0]['image_url']);
    }
}
