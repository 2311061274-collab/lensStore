<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerChatSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();

        // Tạo nhân viên hỗ trợ mặc định
        $this->staff = User::factory()->create([
            'name' => 'Nhân viên LensStore',
            'email' => 'staff@lensstore.com',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Ống kính Prime',
            'slug' => 'ong-kinh-prime',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sony FE 50mm f/1.2 GM',
            'slug' => 'sony-fe-50mm-f12-gm',
            'brand' => 'Sony',
            'mount' => 'Sony E',
            'focal_length' => '50mm',
            'aperture' => 'f/1.2',
            'price' => 48990000,
            'stock' => 10,
            'description' => 'Ống kính chân dung cao cấp',
        ]);
    }

    public function test_guest_can_send_message_and_session_creates_guest_user(): void
    {
        $response = $this->postJson(route('user.chat.send'), [
            'message' => 'Xin chào LensStore!',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['id', 'content', 'sender_name', 'is_mine']);

        // Xác nhận session đã lưu guest user
        $guestId = session('chat_guest_user_id');
        $this->assertNotNull($guestId);

        $guestUser = User::find($guestId);
        $this->assertNotNull($guestUser);
        $this->assertStringStartsWith('Khách #', $guestUser->name);

        // Kiểm tra tin nhắn khách và tin nhắn tự động được lưu trong DB
        $this->assertDatabaseHas('messages', [
            'sender_id' => $guestUser->id,
            'content' => 'Xin chào LensStore!',
        ]);

        // Tin nhắn tự động từ nhân viên
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->staff->id,
            'receiver_id' => $guestUser->id,
        ]);
    }

    public function test_auto_reply_for_keyword_gia_with_and_without_product(): void
    {
        // 1. Khách hỏi giá chung
        $this->postJson(route('user.chat.send'), [
            'message' => 'Shop ơi cho mình hỏi giá các dòng ống kính',
        ])->assertOk();

        $guestId = session('chat_guest_user_id');
        $this->assertDatabaseHas('messages', [
            'receiver_id' => $guestId,
            'content' => 'Dạ các dòng ống kính tại LensStore có mức giá ưu đãi từ khoảng 3.000.000 ₫ đến hơn 50.000.000 ₫ tùy thuộc vào thương hiệu (Sony, Canon, Nikon, Fujifilm, Tamron, Sigma). Bạn đang quan tâm mẫu ống kính nào để em báo giá cụ thể ạ?',
        ]);

        // 2. Khách hỏi giá kèm sản phẩm cụ thể
        $this->postJson(route('user.chat.send'), [
            'message' => 'Ống kính này bao nhiêu tiền vậy shop?',
            'product_id' => $this->product->id,
        ])->assertOk();

        $latestBotReply = Message::where('receiver_id', $guestId)->latest('id')->first();
        $this->assertNotNull($latestBotReply);
        $this->assertStringContainsString('Sony FE 50mm f/1.2 GM', $latestBotReply->content);
        $this->assertStringContainsString('48.990.000 ₫', $latestBotReply->content);
    }

    public function test_auto_reply_for_keyword_loai_may(): void
    {
        $this->postJson(route('user.chat.send'), [
            'message' => 'Lens này hỗ trợ cho loại máy nào vậy bạn?',
        ])->assertOk();

        $guestId = session('chat_guest_user_id');
        $botReply = Message::where('receiver_id', $guestId)->latest('id')->first();
        $this->assertNotNull($botReply);
        $this->assertStringContainsString('Sony (ngàm E/FE)', $botReply->content);
        $this->assertStringContainsString('Canon (ngàm RF/EF)', $botReply->content);
        $this->assertStringContainsString('Nikon (ngàm Z/F)', $botReply->content);
        $this->assertStringContainsString('Fujifilm (ngàm X/GFX)', $botReply->content);
    }

    public function test_staff_can_view_guest_chat_and_reply_from_admin(): void
    {
        // Khách gửi tin nhắn trước
        $this->postJson(route('user.chat.send'), [
            'message' => 'Cần tư vấn đặt hàng gấp ạ',
        ])->assertOk();

        $guestUser = User::where('email', 'like', 'guest_%')->first();
        $this->assertNotNull($guestUser);
        $guestId = $guestUser->id;

        // Nhân viên đăng nhập trang Admin và lấy tin nhắn
        $adminMessagesResponse = $this->actingAs($this->staff)
            ->getJson(route('admin.chat.messages', $guestId));

        $adminMessagesResponse->assertOk();
        $adminMessagesResponse->assertJsonFragment(['content' => 'Cần tư vấn đặt hàng gấp ạ']);

        // Nhân viên nhắn tin trả lời lại cho khách
        $adminSendResponse = $this->actingAs($this->staff)
            ->postJson(route('admin.chat.send'), [
                'user_id' => $guestId,
                'message' => 'Dạ chào bạn, nhân viên đã sẵn sàng hỗ trợ bạn đây ạ!',
            ]);

        $adminSendResponse->assertOk();

        // Đăng xuất nhân viên để giả lập khách vãng lai nhận tin nhắn
        auth()->logout();

        // Khách (không đăng nhập, dựa vào session) nạp tin nhắn và thấy câu trả lời của nhân viên
        $guestMessagesResponse = $this->withSession(['chat_guest_user_id' => $guestId])
            ->getJson(route('user.chat.messages'));
        $guestMessagesResponse->assertOk();
        $guestMessagesResponse->assertJsonFragment(['content' => 'Dạ chào bạn, nhân viên đã sẵn sàng hỗ trợ bạn đây ạ!']);
    }

    public function test_offline_zero_external_api_calls(): void
    {
        $this->postJson(route('user.chat.send'), [
            'message' => 'Cho mình hỏi về loại máy và giá sản phẩm',
            'product_id' => $this->product->id,
        ])->assertOk();

        // Tuyệt đối không phát sinh bất kỳ yêu cầu mạng Http nào
        Http::assertNothingSent();
    }
}
