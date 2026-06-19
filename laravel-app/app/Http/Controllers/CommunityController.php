<?php

namespace App\Http\Controllers;

use App\Services\CommunityService;
use App\Services\GamificationService;
use App\Models\CommunityPost;
use App\Models\CommunitySticker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($reactable instanceof CommunityPost) {
            if (! $this->communityService->canUserAccessPost(Auth::user(), $reactable)) {
                abort(403, 'Acesso negado a esta publicação.');
            }
        } elseif ($reactable->post && ! $this->communityService->canUserAccessPost(Auth::user(), $reactable->post)) {
            abort(403, 'Acesso negado a esta publicação.');
        }

        $reaction = $reactable->reactions()->where('user_id', Auth::id())->where('emoji', $request->emoji)->first();

        if ($reaction) {
            $reaction->delete();
            return response()->json(['status' => 'removed']);
        }

        $reactable->reactions()->create([
            'user_id' => Auth::id(),
            'emoji' => $request->emoji,
        ]);

        return response()->json(['status' => 'added']);
    }

    /**
     * Comment on a post.
     */
    public function comment(Request $request, CommunityPost $post)
    {
        $request->validate(['content' => 'required|string|max:500']);

        if (! $this->communityService->canUserAccessPost(Auth::user(), $post)) {
            abort(403, 'Acesso negado a esta publicação.');
        }

        $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $this->communityService->filterInappropriateContent($request->content),
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Comentário enviado!');
    }
}
