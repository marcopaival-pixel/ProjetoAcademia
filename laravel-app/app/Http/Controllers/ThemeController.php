<?php

namespace App\Http\Controllers;

use App\Support\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'in:dark,light'],
            'next' => ['nullable', 'string', 'max:2048'],
        ]);
        $themeVal = $request->input('theme');
        $next = (string) $request->input('next', '');
        if ($next === '') {
            $next = auth()->check() ? 'dashboard' : '';
        }
        $target = Theme::safeRedirectTarget($next);

        $minutes = (int) ceil(Theme::LIFETIME / 60);

        return redirect($target)->cookie(
            Theme::COOKIE,
            $themeVal,
            $minutes,
            '/',
            null,
            $request->secure(),
            false,
            false,
            'lax'
        );
    }
}
