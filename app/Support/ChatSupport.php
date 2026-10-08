<?php

namespace App\Support;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ChatSupport
{
    public static function staffRoles(): array
    {
        return ['admin', 'staff'];
    }

    public static function isStaff(?User $user): bool
    {
        return $user && in_array($user->role, self::staffRoles(), true);
    }

    public static function staffQuery(): Builder
    {
        return User::query()->whereIn('role', self::staffRoles());
    }

    public static function staffIds(): Collection
    {
        return self::staffQuery()->pluck('id');
    }

    public static function defaultAgent(): ?User
    {
        return self::staffQuery()->orderBy('id')->first();
    }

    public static function conversationQuery(int $customerId): Builder
    {
        return Message::query()
            ->with(['sender:id,name,role', 'product:id,name,image,price'])
            ->where(function (Builder $q) use ($customerId) {
                $q->where('sender_id', $customerId)
                    ->orWhere('receiver_id', $customerId);
            })
            ->orderBy('id');
    }

    public static function payload(Message $message, int $viewerId): array
    {
        $product = $message->product;

        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'content' => $message->content,
            'image_url' => $message->image_url ? asset($message->image_url) : null,
            'is_mine' => $message->sender_id === $viewerId,
            'is_staff' => self::isStaff($message->sender),
            'sender_name' => $message->sender?->name ?? 'Người dùng',
            'is_read' => $message->is_read,
            'created_at' => $message->created_at?->timezone(config('app.timezone'))->format('H:i'),
            'created_at_full' => $message->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i'),
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('storefront.show', $product->id),
            ] : null,
        ];
    }
}
