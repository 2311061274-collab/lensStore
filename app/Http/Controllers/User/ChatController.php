<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Support\ChatSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getMessages(Request $request): JsonResponse
    {
        $user = Auth::user();

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

    public function send(Request $request): JsonResponse
    {
        $user = Auth::user();

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
        if ($content === '' && !$request->hasFile('image')) {
            return response()->json(['error' => 'Nội dung tin nhắn hoặc hình ảnh không được để trống'], 422);
        }

        $agent = ChatSupport::defaultAgent();
        if (!$agent) {
            return response()->json(['error' => 'Hiện chưa có nhân viên hỗ trợ. Vui lòng thử lại sau.'], 503);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $imagePath = 'storage/' . $path;
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

        // --- BẮT ĐẦU TÍCH HỢP GEMINI AI ---
        $geminiKey = env('GEMINI_API_KEY');
        $aiDisabled = session('chat_ai_disabled', false);
        
        if (!$aiDisabled) {
            if ($geminiKey && $content) {
            try {
                $aiPrompt = "Bạn là trợ lý AI ảo của cửa hàng thiết bị nhiếp ảnh LensStore. Khách hàng vừa nhắn tin: '{$content}'. Hãy trả lời tư vấn thật ngắn gọn, thân thiện, chuyên nghiệp (tối đa 2-3 câu). Không dùng định dạng markdown phức tạp.";
                
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(8)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$geminiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $aiPrompt]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $aiReply = $response->json('candidates.0.content.parts.0.text');
                    if ($aiReply) {
                        Message::create([
                            'sender_id' => $agent->id,
                            'receiver_id' => $user->id,
                            'product_id' => $data['product_id'] ?? null,
                            'content' => trim($aiReply),
                            'is_read' => false,
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::error('Gemini AI No Text in Response: ' . $response->body());
                    }
                } else {
                    $errorMsg = $response->json('error.message') ?? 'Lỗi không xác định từ máy chủ AI.';
                    \Illuminate\Support\Facades\Log::error('Gemini API Error: ' . $response->body());
                    Message::create([
                        'sender_id' => $agent->id,
                        'receiver_id' => $user->id,
                        'product_id' => $data['product_id'] ?? null,
                        'content' => "🤖 Xin lỗi, tôi không thể trả lời lúc này (Lỗi: " . $errorMsg . ").",
                        'is_read' => false,
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gemini AI Chat Error: ' . $e->getMessage());
                Message::create([
                    'sender_id' => $agent->id,
                    'receiver_id' => $user->id,
                    'product_id' => $data['product_id'] ?? null,
                    'content' => "🤖 Xin lỗi, tôi đang gặp sự cố kết nối mạng (" . $e->getMessage() . ").",
                    'is_read' => false,
                ]);
            }
        } elseif ($content) {
            // Chế độ DEMO khi chưa cài đặt Key
            Message::create([
                'sender_id' => $agent->id,
                'receiver_id' => $user->id,
                'product_id' => $data['product_id'] ?? null,
                'content' => "🤖 Chào bạn, tôi là AI Assistant của LensStore! (Đây là tin nhắn tự động vì bạn chưa nhập GEMINI_API_KEY trong file .env).",
                'is_read' => false,
            ]);
        }
        }
        // --- KẾT THÚC AI ---

        return response()->json(ChatSupport::payload($message, $user->id));
    }

    public function toggleAi(Request $request): JsonResponse
    {
        $disable = $request->boolean('disable_ai');
        session(['chat_ai_disabled' => $disable]);
        return response()->json(['success' => true, 'ai_disabled' => $disable]);
    }

    public function unread(): JsonResponse
    {
        $count = Message::query()
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => $count]);
    }
}
