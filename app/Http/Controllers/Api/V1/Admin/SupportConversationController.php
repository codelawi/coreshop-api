<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Events\SupportMessageSent;
use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use App\Services\ExpoPushService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportConversationController extends Controller
{
    public function __construct(private readonly ExpoPushService $push) {}

    public function index(): JsonResponse
    {
        $adminId = Auth::id();

        $conversations = SupportConversation::withCount([
            'messages as unread_count' => fn ($q) => $q->where('sender_id', '!=', $adminId)->whereNull('read_at'),
        ])
            ->with(['user', 'lastMessage.sender'])
            ->latest('last_message_at')
            ->get()
            ->map(fn ($c) => $this->formatConversation($c));

        return response()->json(['success' => true, 'data' => $conversations]);
    }

    public function show(User $user): JsonResponse
    {
        $adminId = Auth::id();

        $conversation = SupportConversation::firstOrCreate(['user_id' => $user->id]);
        $conversation->loadCount([
            'messages as unread_count' => fn ($q) => $q->where('sender_id', '!=', $adminId)->whereNull('read_at'),
        ]);
        $conversation->load(['user', 'lastMessage.sender']);

        return response()->json(['success' => true, 'data' => $this->formatConversation($conversation)]);
    }

    public function messages(Request $request, SupportConversation $supportConversation): JsonResponse
    {
        $limit = 10;
        $beforeId = $request->query('before_id');

        $query = $supportConversation->messages()
            ->with('sender')
            ->orderByDesc('id');

        if ($beforeId) {
            $query->where('id', '<', (int) $beforeId);
        }

        $items = $query->limit($limit + 1)->get();
        $hasMore = $items->count() > $limit;

        if ($hasMore) {
            $items = $items->take($limit);
        }

        $messages = $items->sortBy('id')->values()->map(fn ($m) => $this->formatMessage($m));

        if (! $beforeId) {
            $supportConversation->messages()
                ->where('sender_id', '!=', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'data' => $messages,
            'meta' => ['has_more' => $hasMore],
        ]);
    }

    public function sendMessage(Request $request, SupportConversation $supportConversation): JsonResponse
    {
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'file', 'max:10240', 'mimes:jpeg,png,webp,jpg,heic,heif'],
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

        $recipient = $supportConversation->user;
        $lang = $recipient->language ?? 'ar';
        $title = $lang === 'ar' ? 'رسالة جديدة من الدعم' : 'New Support Message';
        $pushBody = $type === 'image'
            ? ($lang === 'ar' ? '📷 أرسل فريق الدعم صورة' : '📷 Support team sent a photo')
            : Str::limit($body, 100);

        $this->push->sendToUser($recipient, $title, $pushBody, [
            'type' => 'support_message',
            'conversation_id' => $supportConversation->id,
        ]);

        return response()->json(['success' => true, 'data' => $this->formatMessage($message)], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatConversation(SupportConversation $c): array
    {
        $last = $c->lastMessage;

        return [
            'id' => $c->id,
            'user' => [
                'id' => $c->user->id,
                'name' => $c->user->name,
                'avatar' => $c->user->avatar,
                'role' => $c->user->role,
            ],
            'last_message' => $last ? [
                'id' => $last->id,
                'body' => $last->body,
                'sender_id' => $last->sender_id,
                'created_at' => $last->created_at->toISOString(),
            ] : null,
            'last_message_at' => $c->last_message_at?->toISOString(),
            'unread_count' => (int) ($c->unread_count ?? 0),
            'created_at' => $c->created_at->toISOString(),
        ];
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
