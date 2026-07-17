<?php

namespace App\Http\Controllers;

use App\Services\CommunityService;
use App\Services\GamificationService;
use App\Models\CommunityPost;
use App\Models\CommunitySticker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    public function __construct(
        private readonly CommunityService $communityService
    ) {}

    /**
     * Display the social feed.
     */
    public function index(Request $request, GamificationService $gamificationService)
    {
        $user = Auth::user();
        if ($user->hasRole('paciente') && !$user->isAdministrator()) {
            abort(403, 'Acesso negado.');
        }

        $posts = $this->communityService->getFeed($user);
        $stickers = CommunitySticker::where('is_active', true)->get();
        $rankings = $gamificationService->getSocialRankings();

        return view('community.index', compact('posts', 'stickers', 'rankings'));
    }

    /**
     * Store a new post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required_without_all:images,sticker_id|string|max:1000',
            'images.*' => 'image|max:5120',
            'visibility' => 'required|in:public,clinic,private',
            'sticker_id' => 'nullable|exists:community_stickers,id',
        ]);

        try {
            $this->communityService->createPost(
                Auth::user(),
                $request->only(['content', 'visibility', 'activity_status', 'sticker_id']),
                $request->file('images') ?? []
            );

            return back()->with('success', 'Publicação enviada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao publicar: ' . $e->getMessage());
        }
    }

    /**
     * React to a post or comment.
     */
    public function react(Request $request, string $type, int $id)
    {
        $request->validate(['emoji' => 'required|string']);
        
        $reactable = ($type === 'post') 
            ? CommunityPost::findOrFail($id) 
            : \App\Models\CommunityComment::findOrFail($id);

        $user = Auth::user();
        $visitorKey = $user ? null : hash('sha256', $request->session()->getId().'|'.$request->ip().'|community-reaction');

        $reactionQuery = $reactable->reactions()->where('emoji', $request->emoji);
        if ($user) {
            $reactionQuery->where('user_id', $user->id);
        } else {
            $reactionQuery->where('visitor_key', $visitorKey);
        }

        $reaction = $reactionQuery->first();

        if ($reaction) {
            $reaction->delete();
            return response()->json($this->reactionPayload($reactable, 'removed'));
        }

        $reactable->reactions()->create([
            'user_id' => $user?->id,
            'visitor_key' => $visitorKey,
            'emoji' => $request->emoji,
        ]);

        return response()->json($this->reactionPayload($reactable, 'added'));
    }

    public function reactionCounts(Request $request): JsonResponse
    {
        $ids = collect(explode(',', (string) $request->query('posts', '')))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(50)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json(['posts' => []]);
        }

        $posts = CommunityPost::withCount([
                'reactions',
                'comments',
            ])
            ->whereIn('id', $ids)
            ->get()
            ->mapWithKeys(fn (CommunityPost $post) => [
                $post->id => [
                    'reactions_count' => $post->reactions_count,
                    'comments_count' => $post->comments_count,
                ],
            ]);

        return response()->json(['posts' => $posts]);
    }

    /**
     * Comment on a post.
     */
    public function comment(Request $request, CommunityPost $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'visitor_name' => 'nullable|string|max:80',
        ]);

        $user = Auth::user();
        $visitorKey = $user ? null : hash('sha256', $request->session()->getId().'|'.$request->ip().'|community-comment');

        $post->comments()->create([
            'user_id' => $user?->id,
            'visitor_key' => $visitorKey,
            'visitor_name' => $user ? null : trim((string) $request->input('visitor_name', 'Visitante')),
            'content' => $this->communityService->filterInappropriateContent($request->content),
            'parent_id' => $request->parent_id,
        ]);

        if ($request->expectsJson()) {
            $post->loadCount(['comments', 'reactions']);

            return response()->json([
                'status' => 'added',
                'comments_count' => $post->comments_count,
                'reactions_count' => $post->reactions_count,
            ]);
        }

        return back()->with('success', 'Coment�rio enviado!');
    }
    public function destroy(CommunityPost $post)
    {
        $user = Auth::user();

        if ((int) $post->user_id !== (int) $user->id && ! $user->isAdministrator()) {
            abort(403, 'Você não tem permissão para excluir esta publicação.');
        }

        foreach ($post->media as $media) {
            if ($media->type !== 'sticker' && $media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }
            $media->delete();
        }

        $post->comments()->delete();
        $post->reactions()->delete();
        $post->delete();

        return back()->with('success', 'Publicação excluída com sucesso.');
    }

    private function reactionPayload($reactable, string $status): array
    {
        $reactable->loadCount(['reactions']);

        $payload = [
            'status' => $status,
            'reactions_count' => $reactable->reactions_count,
        ];

        if ($reactable instanceof CommunityPost) {
            $reactable->loadCount(['comments']);
            $payload['comments_count'] = $reactable->comments_count;
        }

        return $payload;
    }
}
