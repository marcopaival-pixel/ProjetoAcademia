<?php

namespace App\Services;

use App\Models\User;
use App\Models\CommunityPost;
use App\Models\CommunityReaction;
use App\Models\CommunityComment;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Get social rankings for the community.
     */
    public function getSocialRankings()
    {
        // Top Motivador (Most reactions received)
        $topMotivadores = User::select('users.id', 'users.name', 'users.avatar')
            ->join('community_posts', 'users.id', '=', 'community_posts.user_id')
            ->join('community_reactions', function($join) {
                $join->on('community_posts.id', '=', 'community_reactions.reactable_id')
                     ->where('community_reactions.reactable_type', '=', CommunityPost::class);
            })
            ->selectRaw('COUNT(community_reactions.id) as social_score')
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('social_score')
            ->take(5)
            ->get();

        // Mestre das Figurinhas (Most posts with stickers)
        $mestreFigurinhas = User::select('users.id', 'users.name', 'users.avatar')
            ->join('community_posts', 'users.id', '=', 'community_posts.user_id')
            ->join('community_post_media', 'community_posts.id', '=', 'community_post_media.post_id')
            ->where('community_post_media.type', 'sticker')
            ->selectRaw('COUNT(community_post_media.id) as social_score')
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('social_score')
            ->take(5)
            ->get();

        // Influenciador (Most comments received on their posts)
        $influenciadores = User::select('users.id', 'users.name', 'users.avatar')
            ->join('community_posts', 'users.id', '=', 'community_posts.user_id')
            ->join('community_comments', 'community_posts.id', '=', 'community_comments.post_id')
            ->selectRaw('COUNT(community_comments.id) as social_score')
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('social_score')
            ->take(5)
            ->get();

        return [
            'top_motivadores' => $topMotivadores,
            'mestre_figurinhas' => $mestreFigurinhas,
            'influenciadores' => $influenciadores,
        ];
    }
}
