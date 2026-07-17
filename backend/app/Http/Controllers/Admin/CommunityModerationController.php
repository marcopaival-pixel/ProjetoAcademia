<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityReport;
use App\Models\CommunitySticker;
use Illuminate\Http\Request;

class CommunityModerationController extends Controller
{
    /**
     * Display community moderation dashboard.
     */
    public function index()
    {
        $pendingPosts = CommunityPost::with('user')->where('status', 'pending')->latest()->get();
        $reports = CommunityReport::with(['post', 'user'])->where('status', 'pending')->latest()->get();
        $stickers = CommunitySticker::all();

        return view('admin.community.index', compact('pendingPosts', 'reports', 'stickers'));
    }

    /**
     * Approve or reject a post.
     */
    public function updatePostStatus(Request $request, CommunityPost $post)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $post->update(['status' => $request->status]);

        return back()->with('success', 'Status da publicação atualizado.');
    }

    /**
     * Resolve a report.
     */
    public function resolveReport(Request $request, CommunityReport $report)
    {
        $request->validate(['action' => 'required|in:delete_post,dismiss']);

        if ($request->action === 'delete_post') {
            $report->post->delete();
            $report->update(['status' => 'resolved']);
        } else {
            $report->update(['status' => 'dismissed']);
        }

        return back()->with('success', 'Denúncia resolvida.');
    }

    /**
     * Manage stickers.
     */
    public function storeSticker(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'file' => 'required|image|max:2048',
        ]);

        $path = $request->file('file')->store('images/stickers', 'public');

        CommunitySticker::create([
            'name' => $request->name,
            'category' => $request->category,
            'path' => $path,
        ]);

        return back()->with('success', 'Figurinha adicionada.');
    }
}
