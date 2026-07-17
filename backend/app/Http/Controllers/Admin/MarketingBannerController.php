<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingBanner;
use App\Models\MarketingBannerClick;
use App\Models\MarketingBannerDismissal;
use App\Models\MarketingBannerView;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MarketingBannerController extends Controller
{
    public function index()
    {
        $banners = MarketingBanner::withCount(['views', 'clicks', 'dismissals'])
            ->orderBy('priority', 'desc')
            ->latest()
            ->paginate(15);

        $stats = [
            'active_count' => MarketingBanner::active()->count(),
            'total_views' => MarketingBannerView::count(),
            'total_clicks' => MarketingBannerClick::count(),
            'avg_ctr' => MarketingBannerView::count() > 0 
                ? round((MarketingBannerClick::count() / MarketingBannerView::count()) * 100, 2) 
                : 0,
        ];

        return view('admin.marketing.banners.index', compact('banners', 'stats'));
    }

    public function create()
    {
        $roles = Role::orderBy('label')->get();
        if ($roles->isEmpty()) {
            $roles = Role::orderBy('name')->get();
        }
        return view('admin.marketing.banners.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'background_color' => 'required|string',
            'icon' => 'nullable|string',
            'primary_button_text' => 'nullable|string',
            'primary_button_link' => 'nullable|string',
            'secondary_button_text' => 'nullable|string',
            'secondary_button_link' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'priority' => 'required|integer',
            'is_active' => 'boolean',
            'allow_dismiss' => 'boolean',
            'dont_show_again_option' => 'boolean',
            'display_type' => 'required|in:once,until_closed,always,frequency',
            'frequency_days' => 'required_if:display_type,frequency|integer|min:0',
            'image_desktop' => 'nullable|image|max:2048',
            'image_mobile' => 'nullable|image|max:1024',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($request->hasFile('image_desktop')) {
            $validated['image_desktop'] = Storage::url($request->file('image_desktop')->store('banners', 'public'));
        }

        if ($request->hasFile('image_mobile')) {
            $validated['image_mobile'] = Storage::url($request->file('image_mobile')->store('banners', 'public'));
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['allow_dismiss'] = $request->has('allow_dismiss');
        $validated['dont_show_again_option'] = $request->has('dont_show_again_option');

        DB::transaction(function () use ($validated, $request) {
            $banner = MarketingBanner::create($validated);
            $banner->roles()->sync($request->roles);
        });

        return redirect()->route('admin.marketing.banners.index')->with('success', 'Banner criado com sucesso!');
    }

    public function edit(MarketingBanner $banner)
    {
        $roles = Role::orderBy('label')->get();
        if ($roles->isEmpty()) {
            $roles = Role::orderBy('name')->get();
        }
        $banner->load('roles');
        return view('admin.marketing.banners.edit', compact('banner', 'roles'));
    }

    public function update(Request $request, MarketingBanner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'background_color' => 'required|string',
            'icon' => 'nullable|string',
            'primary_button_text' => 'nullable|string',
            'primary_button_link' => 'nullable|string',
            'secondary_button_text' => 'nullable|string',
            'secondary_button_link' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'priority' => 'required|integer',
            'display_type' => 'required|in:once,until_closed,always,frequency',
            'frequency_days' => 'required_if:display_type,frequency|integer|min:0',
            'image_desktop' => 'nullable|image|max:2048',
            'image_mobile' => 'nullable|image|max:1024',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($request->hasFile('image_desktop')) {
            $validated['image_desktop'] = Storage::url($request->file('image_desktop')->store('banners', 'public'));
        }

        if ($request->hasFile('image_mobile')) {
            $validated['image_mobile'] = Storage::url($request->file('image_mobile')->store('banners', 'public'));
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['allow_dismiss'] = $request->has('allow_dismiss');
        $validated['dont_show_again_option'] = $request->has('dont_show_again_option');

        DB::transaction(function () use ($banner, $validated, $request) {
            $banner->update($validated);
            $banner->roles()->sync($request->roles);
        });

        return redirect()->route('admin.marketing.banners.index')->with('success', 'Banner atualizado com sucesso!');
    }

    public function destroy(MarketingBanner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.marketing.banners.index')->with('success', 'Banner removido com sucesso!');
    }

    public function toggleStatus(MarketingBanner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return response()->json(['status' => 'success', 'is_active' => $banner->is_active]);
    }

    // API Tracking
    public function trackView(Request $request, MarketingBanner $banner)
    {
        MarketingBannerView::create([
            'banner_id' => $banner->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function trackClick(Request $request, MarketingBanner $banner)
    {
        MarketingBannerClick::create([
            'banner_id' => $banner->id,
            'user_id' => auth()->id(),
            'button_type' => $request->button_type ?? 'primary',
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function trackDismiss(Request $request, MarketingBanner $banner)
    {
        MarketingBannerDismissal::create([
            'banner_id' => $banner->id,
            'user_id' => auth()->id(),
            'dont_show_again' => $request->has('dont_show_again'),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
