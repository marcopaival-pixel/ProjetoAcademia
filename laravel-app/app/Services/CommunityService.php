<?php

namespace App\Services;

use App\Models\CommunityPost;
use App\Models\CommunityPostMedia;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommunityService
{
    /**
     * Create a new post.
     */
    public function createPost(User $user, array $data, array $files = []): CommunityPost
    {
        $content = $this->filterInappropriateContent($data['content'] ?? '');
        
        $status = SystemSetting::isTrue('community_auto_approve', true) ? 'approved' : 'pending';

        $post = CommunityPost::create([
            'user_id' => $user->id,
            'academy_company_id' => $user->academy_company_id,
            'content' => $content,
            'status' => $status,
            'visibility' => $data['visibility'] ?? 'public',
            'activity_status' => $data['activity_status'] ?? null,
            'hashtags' => $this->extractHashtags($content),
            'scheduled_at' => $data['scheduled_at'] ?? null,
        ]);

        foreach ($files as $file) {
            $path = $file->store('community/posts', 'public');
            CommunityPostMedia::create([
                'post_id' => $post->id,
                'file_path' => $path,
                'type' => 'image',
            ]);
        }

        if (isset($data['sticker_id'])) {
            $sticker = \App\Models\CommunitySticker::find($data['sticker_id']);
            if ($sticker) {
                CommunityPostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => $sticker->path,
                    'type' => 'sticker',
                ]);
            }
        }

        return $post;
    }

    /**
     * Simple bad word filter.
     */
    public function filterInappropriateContent(string $content): string
    {
        $blockedWordsStr = SystemSetting::get('community_blocked_words', '');
        if (empty($blockedWordsStr)) {
            return $content;
        }

        $blockedWords = array_map('trim', explode(',', $blockedWordsStr));
        
        foreach ($blockedWords as $word) {
            if (empty($word)) continue;
            $replacement = str_repeat('*', strlen($word));
            $content = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', $replacement, $content);
        }

        return $content;
    }

    /**
     * Extract hashtags from content.
     */
    private function extractHashtags(string $content): array
    {
        preg_match_all('/#(\w+)/u', $content, $matches);
        return array_unique($matches[1] ?? []);
    }

    /**
     * Get feed posts based on user context.
     */
    public function getFeed(User $user, int $perPage = 10)
    {
        return CommunityPost::with(['user', 'media', 'reactions', 'comments' => function($q) {
                $q->with('user')->latest()->limit(3);
            }])
            ->where('status', 'approved')
            ->where(function($query) use ($user) {
                $query->where('visibility', 'public')
                    ->orWhere(function($q) use ($user) {
                        $q->where('visibility', 'clinic')
                          ->where('academy_company_id', $user->academy_company_id);
                    })
                    ->orWhere('user_id', $user->id);
            })
            ->latest()
            ->paginate($perPage);
    }
}
