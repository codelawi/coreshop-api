<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Events\AdminNotificationCreated;
use App\Events\SupportMessageSent;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function conversation(): JsonResponse
    {
        $conversation = SupportConversation::firstOrCreate(['user_id' => Auth::id()]);

        return response()->json([
            'success' => true,
            'data' => ['id' => $conversation->id],
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $conversation = SupportConversation::where('user_id', Auth::id())->first();

        if (! $conversation) {
            return response()->json(['success' => true, 'data' => ['count' => 0]]);
        }

        $count = $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['success' => true, 'data' => ['count' => $count]]);
    }

    public function messages(SupportConversation $supportConversation, Request $request): JsonResponse
    {
        abort_unless((int) $supportConversation->user_id === (int) Auth::id(), 403);

        $beforeId = $request->query('before_id');
        $limit = min((int) $request->query('limit', 50), 100);

        $query = $supportConversation->messages()->with('sender');

        if ($beforeId) {
            $query->where('id', '<', (int) $beforeId);
        }

        $rawMessages = $query->latest('id')->limit($limit + 1)->get();
        $hasMore = $rawMessages->count() > $limit;
        $messages = $rawMessages->take($limit)->reverse()->values()->map(fn ($m) => $this->formatMessage($m));

        $supportConversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'data' => $messages, 'meta' => ['has_more' => $hasMore]]);
    }

    public function sendMessage(Request $request, SupportConversation $supportConversation): JsonResponse
    {
        abort_unless((int) $supportConversation->user_id === (int) Auth::id(), 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'file', 'max:10240', 'mimes:jpeg,png,webp,jpg,heic,heif'],
            'type' => ['nullable', 'string', 'in:text,image'],
        ]);

        abort_if(empty($data['body']) && ! $request->hasFile('image'), 422);

        $type = 'text';
        $body = $data['body'] ?? '';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'chat/'.Str::uuid().'.jpg';

            /** @var FilesystemAdapter $disk */
            $disk = Storage::disk('s3');
            $disk->put($filename, $file->get(), ['ContentType' => 'image/jpeg']);
            $body = rtrim(config('filesystems.disks.s3.url'), '/').'/'.$filename;
            $type = 'image';
        }

        $message = $supportConversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $body,
            'type' => $type,
        ]);

        $supportConversation->update(['last_message_at' => now()]);
        $message->load('sender');

        SupportMessageSent::dispatch($message);

        $previewBody = $type === 'image' ? '📷 Photo' : Str::limit($body, 100);
        $notification = AdminNotification::create([
            'type' => 'new_support_message',
            'title' => "{$message->sender->name}",
            'body' => $previewBody,
            'data' => ['conversation_id' => $supportConversation->id],
        ]);
        AdminNotificationCreated::dispatch($notification);

        return response()->json(['success' => true, 'data' => $this->formatMessage($message)], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMessage(SupportMessage $m): array
    {
        return [
            'id' => $m->id,
            'support_conversation_id' => $m->support_conversation_id,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->sender->name,
            'sender_avatar' => $m->sender->avatar,
            'sender_role' => $m->sender->role,
            'type' => $m->type ?? 'text',
            'body' => $m->body,
            'read_at' => $m->read_at?->toISOString(),
            'created_at' => $m->created_at->toISOString(),
        ];
    }
}
