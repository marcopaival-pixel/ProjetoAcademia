<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ShopPointsWallet;
use App\Models\User;
use App\Services\Shop\ShopPointsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopAdminPointsController extends Controller
{
    public function __construct(private ShopPointsService $pointsService) {}

    public function index(Request $request): View
    {
        $user = null;
        $wallet = null;
        $recentTransactions = collect();

        if ($request->filled('user_id')) {
            $user = User::find($request->integer('user_id'));
            if ($user) {
                try {
                    $wallet = $this->pointsService->getOrCreateWallet($user);
                    $recentTransactions = $wallet->transactions()->orderByDesc('id')->limit(20)->get();
                } catch (\RuntimeException) {
                    $wallet = null;
                }
            }
        } elseif ($request->filled('search')) {
            $search = trim((string) $request->get('search'));
            $user = User::query()
                ->where('email', $search)
                ->orWhere('id', is_numeric($search) ? (int) $search : 0)
                ->orWhere('name', 'like', "%{$search}%")
                ->first();
            if ($user) {
                try {
                    $wallet = $this->pointsService->getOrCreateWallet($user);
                    $recentTransactions = $wallet->transactions()->orderByDesc('id')->limit(20)->get();
                } catch (\RuntimeException) {
                    $wallet = null;
                }
            }
        }

        $topWallets = ShopPointsWallet::query()
            ->with('user:id,name,email')
            ->orderByDesc('balance_points')
            ->limit(10)
            ->get();

        return view('admin.shop.points.index', compact('user', 'wallet', 'recentTransactions', 'topWallets'));
    }

    public function credit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'points' => ['required', 'integer', 'min:1', 'max:1000000'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $this->pointsService->credit(
            $user,
            (int) $validated['points'],
            'Crédito admin: '.$validated['reason'],
            'admin',
            auth()->id()
        );

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Creditou {$validated['points']} pontos shopping ao utilizador #{$user->id}",
            'ip_address' => $request->ip(),
            'payload' => [
                'target_user_id' => $user->id,
                'points' => $validated['points'],
                'reason' => $validated['reason'],
            ],
        ]);

        return redirect()
            ->route('admin.shop.points.index', ['user_id' => $user->id])
            ->with('success', "Foram creditados {$validated['points']} pontos para {$user->name}.");
    }
}
