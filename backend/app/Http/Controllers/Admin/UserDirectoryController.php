<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminOverviewStats;
use Illuminate\View\View;

class UserDirectoryController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderByDesc('created_at')
            ->simplePaginate(40)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'overview' => AdminOverviewStats::collect(),
        ]);
    }
}
