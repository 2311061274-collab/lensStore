<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Support\ChatSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        abort_unless(ChatSupport::isStaff(Auth::user()), 403);

        return view('admin.chat.index');
    }

    public function getUsers(): JsonResponse
    {
        abort_unless(ChatSupport::isStaff(Auth::user()), 403);

        $staffIds = ChatSupport::staffIds();

        $rows = Message::query()
            ->select('id', 'sender_id', 'receiver_id', 'content', 'is_read', 'created_at')
            ->where(function ($q) use ($staffIds) {
                $q->whereIn('sender_id', $staffIds)
                    ->orWhereIn('receiver_id', $staffIds);
            })
            ->orderByDesc('id')
            ->get();

        $threads = [];
        foreach ($rows as $msg) {
            $customerId = $staffIds->contains($msg->sender_id)
                ? $msg->receiver_id
                : $msg->sender_id;

            if ($staffIds->contains($customerId)) {
                continue;
            }

            if (! isset($threads[$customerId])) {
                $threads[$customerId] = [
                    'last' => $msg,
                    'unread' => 0,
                ];
            }

            if (! $msg->is_read && ! $staffIds->contains($msg->sender_id)) {
                $threads[$customerId]['unread']++;
            }
        }

        $users = User::query()
            ->whereIn('id', array_keys($threads))
            ->get(['id', 'name', 'email'])
            ->keyBy('id');

        $payload = collect($threads)
            ->map(function (array $thread, $customerId) use ($users) {
                $user = $users->get((int) $customerId);
                if (! $user) {
                    return null;
                }

                $last = $thread['last'];

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'unread' => $thread['unread'],
                    'last_message' => Str::limit($last->content, 60),
                    'last_at' => $last->created_at?->timezone(config('app.timezone'))->format('H:i'),
                    'last_id' => $last->id,
                ];
            })
            ->filter()
            ->sortByDesc('last_id')
            ->values();

        $totalUnread = $payload->sum('unread');

        return response()->json([
            'users' => $payload,
            'unread' => $totalUnread,
        ]);
    }

    public function getMessages(Request $request, int $userId): JsonResponse
    {
        abort_unless(ChatSupport::isStaff(Auth::user()), 403);

        $customer = User::query()->findOrFail($userId);
        abort_if(ChatSupport::isStaff($customer), 422, 'Chỉ chat với khách hàng.');

        $sinceId = (int) $request->query('since_id', 0);
        $query = ChatSupport::conversationQuery($customer->id);

        if ($sinceId > 0) {
            $query->where('id', '>', $sinceId);
        }

        $messages = $query->get();

        Message::query()
            ->where('sender_id', $customer->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'user' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'messages' => $messages->map(fn (Message $msg) => ChatSupport::payload($msg, Auth::id()))->values(),
            'last_id' => (int) ($messages->max('id') ?: $sinceId),
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        abort_unless(ChatSupport::isStaff(Auth::user()), 403);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:2048',
        ]);

        $customer = User::query()->findOrFail($data['user_id']);
        abort_if(ChatSupport::isStaff($customer), 422, 'Không thể gửi tin cho nhân viên khác tại đây.');

        $content = trim($data['message'] ?? '');
        if ($content === '' && ! $request->hasFile('image')) {
            return response()->json(['error' => 'Nội dung tin nhắn hoặc hình ảnh không được để trống'], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $imagePath = 'storage/'.$path;
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $customer->id,
            'content' => $content,
            'image_url' => $imagePath,
            'is_read' => false,
        ]);

        $message->load(['sender:id,name,role', 'product:id,name,image,price']);

        return response()->json(ChatSupport::payload($message, Auth::id()));
    }
}
