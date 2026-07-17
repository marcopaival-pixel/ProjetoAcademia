<?php

namespace App\Services;

use App\Models\MarketingBanner;
use App\Models\MarketingBannerDismissal;
use App\Models\MarketingBannerView;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MarketingBannerService
{
    public function getActiveBannerForUser(User $user)
    {
        $roleIds = $user->roles->pluck('id')->toArray();

        // 1. Get all potential active banners for these roles
        $banners = MarketingBanner::active()
            ->whereHas('roles', function ($q) use ($roleIds) {
                $q->whereIn('roles.id', $roleIds);
            })
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($banners as $banner) {
            if ($this->shouldShowBanner($banner, $user)) {
                return $banner;
            }
        }

        return null;
    }

    private function shouldShowBanner(MarketingBanner $banner, User $user)
    {
        // Check "Don't show again"
        $dismissal = MarketingBannerDismissal::where('banner_id', $banner->id)
            ->where('user_id', $user->id)
            ->first();

        if ($dismissal && $dismissal->dont_show_again) {
            return false;
        }

        // Check display rules
        switch ($banner->display_type) {
            case 'once':
                // Show if never viewed
                return !MarketingBannerView::where('banner_id', $banner->id)
                    ->where('user_id', $user->id)
                    ->exists();

            case 'until_closed':
                // Show if never dismissed
                return !$dismissal;

            case 'frequency':
                // Show if never dismissed OR last dismissal was more than X days ago
                if (!$dismissal) return true;
                return $dismissal->created_at->addDays($banner->frequency_days)->isPast();

            case 'always':
            default:
                return true;
        }
    }
}
