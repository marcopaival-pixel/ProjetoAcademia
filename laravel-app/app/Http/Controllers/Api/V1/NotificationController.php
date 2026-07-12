<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use FormatsApiResponses;

    public function unreadCounts(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;

        $emails = 0;
        $messages = Message::whereHas('conversation', function ($q) use ($userId) {
            $q->where('user_one_id', $userId)->orWhere('user_two_id', $userId);
        })
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->whereHas('sender', function ($q) use ($userId) {
                $q->whereDoesntHave('blockers', function ($sq) use ($userId) {
                    $sq->where('blocker_id', $userId);
                });
            })
            ->count();

        return $this->success([
            'emails' => $emails,
            'messages' => $messages,
            'total' => $emails + $messages,
        ]);
    }
}
