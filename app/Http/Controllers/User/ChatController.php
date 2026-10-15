<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use App\Support\ChatSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Lấy danh sách tin nhắn hội thoại của người dùng (thành viên hoặc khách vãng lai)
     */
    public function getMessages(Request $request): JsonResponse
    {
        $user = $this->resolveUser();

        if (! $user) {
            return response()->json([
                'messages' => [],
                'last_id' => 0,
                'unread' => 0,
                'agent_name' => ChatSupport::defaultAgent()?->name ?? 'LensStore',
            ]);
        }

        if (ChatSupport::isStaff($user)) {
            return response()->json([
                'error' => 'Nhân viên vui lòng trả lời khách từ trang quản lý.',
            ], 403);
        }

        $userId = $user->id;
        $sinceId = (int) $request->query('since_id', 0);

        $query = ChatSupport::conversationQuery($userId);

        if ($sinceId > 0) {
            $query->where('id', '>', $sinceId);
        }

        $unread = Message::query()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        $messages = $query->get();

        if ($request->boolean('mark_read')) {
            Message::query()
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
            $unread = 0;
        }

        return response()->json([
            'messages' => $messages->map(fn (Message $msg) => ChatSupport::payload($msg, $userId))->values(),
            'last_id' => (int) ($messages->max('id') ?: $sinceId),
            'unread' => $unread,
            'agent_name' => ChatSupport::defaultAgent()?->name ?? 'LensStore',
        ]);
    }

    /**
     * Gửi tin nhắn từ phía khách hàng và kích hoạt phản hồi tự động ngoại tuyến theo từ khóa
     */
    public function send(Request $request): JsonResponse
    {
        $user = $this->getOrCreateUser();

        if (ChatSupport::isStaff($user)) {
            return response()->json([
                'error' => 'Nhân viên vui lòng trả lời khách từ trang quản lý.',
            ], 403);
        }

        $data = $request->validate([
            'message' => 'nullable|string|max:2000',
            'product_id' => 'nullable|exists:products,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $content = trim($data['message'] ?? '');
        if ($content === '' && ! $request->hasFile('image')) {
            return response()->json(['error' => 'Nội dung tin nhắn hoặc hình ảnh không được để trống'], 422);
        }

        $agent = ChatSupport::defaultAgent();
        if (! $agent) {
            return response()->json(['error' => 'Hiện chưa có nhân viên hỗ trợ. Vui lòng thử lại sau.'], 503);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $imagePath = 'storage/'.$path;
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $agent->id,
            'product_id' => $data['product_id'] ?? null,
            'content' => $content,
            'image_url' => $imagePath,
            'is_read' => false,
        ]);

        $message->load(['sender:id,name,role', 'product:id,name,image,price']);

        // Phản hồi tự động fix cứng ngoại tuyến (không gọi API bên ngoài)
        if ($content !== '') {
            $autoReply = $this->getAutoReply($content, $data['product_id'] ?? null);
            if ($autoReply) {
                Message::create([
                    'sender_id' => $agent->id,
                    'receiver_id' => $user->id,
                    'product_id' => $data['product_id'] ?? null,
                    'content' => $autoReply,
                    'is_read' => false,
                ]);
            }
        }

        return response()->json(ChatSupport::payload($message, $user->id));
    }

    /**
     * Lấy số lượng tin nhắn chưa đọc
     */
    public function unread(): JsonResponse
    {
        $user = $this->resolveUser();
        if (! $user) {
            return response()->json(['unread' => 0]);
        }

        $count = Message::query()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => $count]);
    }

    /**
     * Xác định người dùng hiện tại (Auth user hoặc Session guest)
     */
    protected function resolveUser(): ?User
    {
        $user = Auth::user();
        if ($user) {
            return $user;
        }

        $guestId = session('chat_guest_user_id');
        if ($guestId) {
            $guest = User::find($guestId);
            if ($guest) {
                return $guest;
            }
        }

        return null;
    }

    /**
     * Lấy hoặc tự động sinh tài khoản khách nhẹ nhàng theo Session
     */
    protected function getOrCreateUser(): User
    {
        $user = $this->resolveUser();
        if ($user) {
            return $user;
        }

        $code = strtoupper(Str::random(4));
        $user = User::create([
            'name' => 'Khách #'.$code,
            'email' => 'guest_'.strtolower($code).'_'.time().'@lensstore.local',
            'password' => bcrypt(Str::random(16)),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        session(['chat_guest_user_id' => $user->id]);

        return $user;
    }

    /**
     * Xử lý câu trả lời tự động fix cứng theo từ khóa (ngoại tuyến, không kết nối API)
     */
    protected function getAutoReply(string $content, ?int $productId = null): string
    {
        $lower = mb_strtolower($content, 'UTF-8');

        // 1. Từ khóa về GIÁ
        if (str_contains($lower, 'giá') || str_contains($lower, 'bao nhiêu') || str_contains($lower, 'nhiêu tiền') || str_contains($lower, 'chi phí') || str_contains($lower, 'price')) {
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $formattedPrice = number_format($product->price, 0, ',', '.').' ₫';

                    return "Dạ sản phẩm '{$product->name}' hiện có giá là {$formattedPrice} (hàng chính hãng mới 100%, kèm bảo hành). Nhân viên LensStore sẽ hỗ trợ tư vấn chi tiết cho bạn ngay nhé!";
                }
            }

            return 'Dạ các dòng ống kính tại LensStore có mức giá ưu đãi từ khoảng 3.000.000 ₫ đến hơn 50.000.000 ₫ tùy thuộc vào thương hiệu (Sony, Canon, Nikon, Fujifilm, Tamron, Sigma). Bạn đang quan tâm mẫu ống kính nào để em báo giá cụ thể ạ?';
        }

        // 2. Từ khóa về LOẠI MÁY
        if (str_contains($lower, 'loại máy') || str_contains($lower, 'máy ảnh') || str_contains($lower, 'body') || str_contains($lower, 'ngàm') || str_contains($lower, 'hãng máy')) {
            return 'Dạ LensStore cung cấp ống kính chuyên dụng cho các loại máy ảnh phổ biến hiện nay: Sony (ngàm E/FE), Canon (ngàm RF/EF), Nikon (ngàm Z/F) và Fujifilm (ngàm X/GFX). Bạn đang sử dụng loại máy ảnh hay ngàm nào để em tư vấn dòng lens tương thích chuẩn nhất ạ?';
        }

        // 3. Từ khóa chào hỏi cơ bản
        if (str_contains($lower, 'chào') || str_contains($lower, 'hi') || str_contains($lower, 'hello') || str_contains($lower, 'alo')) {
            return 'Dạ LensStore xin chào bạn! Cửa hàng chuyên ống kính chính hãng Sony, Canon, Nikon, Fujifilm... Bạn cần tư vấn về loại máy, giá bán hay chính sách bảo hành ạ?';
        }

        // 4. Mặc định: thông báo đã tiếp nhận
        return 'Dạ LensStore đã nhận được câu hỏi của bạn. Nhân viên hỗ trợ sẽ nhắn tin phản hồi bạn trong giây lát nhé!';
    }
}
