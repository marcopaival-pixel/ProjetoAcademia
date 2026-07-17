<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Services\CommunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function __construct(private readonly CommunityService $communityService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->hasRole('paciente') && ! $user->isAdministrator()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $posts = $this->communityService->getFeed($user, 20);

        return response()->json([
            'data' => [
                'posts' => $posts->getCollection()->map(fn (CommunityPost $post) => $this->postPayload($post))->values(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->hasRole('paciente') && ! $user->isAdministrator()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
            'visibility' => ['nullable', 'in:public,clinic,private'],
        ]);

        $post = $this->communityService->createPost($user, [
            'content' => $validated['content'],
            'visibility' => $validated['visibility'] ?? 'public',
        ]);

        $post->load(['user', 'media', 'reactions', 'comments.user']);

        return response()->json([
            'data' => [
                'post' => $this->postPayload($post),
            ],
        ], 201);
    }

    public function comment(Request $request, CommunityPost $post): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:500'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $this->communityService->filterInappropriateContent($validated['content']),
        ]);

        $comment->load('user');

        return response()->json([
            'data' => [
                'comment' => [
                    'id' => $comment->id,
                    'author_name' => $comment->user?->name,
                    'content' => $comment->content,
                    'created_at' => optional($comment->created_at)->toISOString(),
                ],
            ],
        ], 201);
    }

    private function postPayload(CommunityPost $post): array
    {
        return [
            'id' => $post->id,
            'author_name' => $post->user?->name,
            'content' => $post->content,
            'visibility' => $post->visibility,
            'status' => $post->status,
            'created_at' => optional($post->created_at)->toISOString(),
            'reactions_count' => $post->reactions->count(),
            'comments_count' => $post->comments->count(),
            'comments' => $post->comments->map(fn ($comment) => [
                'id' => $comment->id,
                'author_name' => $comment->user?->name,
                'content' => $comment->content,
                'created_at' => optional($comment->created_at)->toISOString(),
            ])->values(),
        ];
    }
}
