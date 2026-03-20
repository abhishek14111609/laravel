<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::latest('created_at')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Admin account cannot be blocked here.');
        }

        $user->update(['is_blocked' => ! $user->is_blocked]);

        return back()->with('success', 'User status updated.');
    }
}
