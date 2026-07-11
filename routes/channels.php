<?php

use App\Models\Conversation;
use App\Models\SupportConversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin-notifications', function ($user) {
    return $user->role === 'admin';
});

Broadcast::channel('support.{conversationId}', function ($user, $conversationId) {
    $conversation = SupportConversation::find($conversationId);

    if (! $conversation) {
        return false;
    }

    return $user->role === 'admin' || $conversation->user_id === $user->id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (! $conversation) {
        return false;
    }

    if ($conversation->client_id === $user->id) {
        return true;
    }

    return $user->store && $conversation->store_id === $user->store->id;
});
