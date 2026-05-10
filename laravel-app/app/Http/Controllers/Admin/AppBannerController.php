<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\AppLaunchLead;
use App\Models\AppBannerMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppBannerController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('label')->get();
        if ($roles->isEmpty()) {
            $roles = Role::orderBy('name')->get();
        }

        $settings = [
            'enabled' => SystemSetting::isTrue('app_banner_enabled', false),
            'title' => SystemSetting::get('app_banner_title', '🚀 Em breve: Aplicativo Oficial do NexShape'),
            'description' => SystemSetting::get('app_banner_description', 'Tenha seus treinos, dieta, consultas, agenda, evolução e inteligência artificial na palma da sua mão.'),
            'launch_date' => SystemSetting::get('app_banner_launch_date', ''),
            'image_url' => SystemSetting::get('app_banner_image', ''),
            'google_play_link' => SystemSetting::get('app_banner_google_play_link', '#'),
            'apple_store_link' => SystemSetting::get('app_banner_apple_store_link', '#'),
            'roles' => json_decode(SystemSetting::get('app_banner_roles', '[]'), true),
        ];

        $metrics = [
            'views' => AppBannerMetric::where('event_type', 'view')->count(),
            'clicks' => AppBannerMetric::where('event_type', 'click_cta')->count(),
            'leads' => AppLaunchLead::count(),
        ];

        return view('admin.marketing.app_banner', compact('settings', 'metrics', 'roles'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'launch_date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'roles' => 'nullable|array',
        ]);

        SystemSetting::set('app_banner_enabled', $request->has('enabled') ? 'true' : 'false');
        SystemSetting::set('app_banner_title', $request->title);
        SystemSetting::set('app_banner_description', $request->description);
        SystemSetting::set('app_banner_launch_date', $request->launch_date ?? '');
        SystemSetting::set('app_banner_google_play_link', $request->google_play_link ?? '#');
        SystemSetting::set('app_banner_apple_store_link', $request->apple_store_link ?? '#');
        SystemSetting::set('app_banner_roles', json_encode($request->roles ?? []));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('marketing', 'public');
            SystemSetting::set('app_banner_image', Storage::url($path));
        }

        return redirect()->back()->with('success', 'Configurações do banner atualizadas com sucesso.');
    }

    public function leads()
    {
        $leads = AppLaunchLead::latest()->paginate(20);
        return view('admin.marketing.app_leads', compact('leads'));
    }

    // API endpoints for frontend interaction
    public function registerLead(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        AppLaunchLead::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => auth()->id(),
            'source' => 'dashboard_banner',
        ]);

        AppBannerMetric::create([
            'event_type' => 'form_submit',
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Obrigado! Avisaremos você em breve.']);
    }

    public function trackMetric(Request $request)
    {
        $request->validate([
            'event_type' => 'required|string',
        ]);

        AppBannerMetric::create([
            'event_type' => $request->event_type,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
