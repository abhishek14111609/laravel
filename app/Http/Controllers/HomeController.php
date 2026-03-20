<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $events = collect();
        if (Schema::hasTable('events')) {
            $events = Event::with('category')
                ->withAvg('reviews', 'rating')
                ->active()
                ->latest()
                ->take(6)
                ->get();
        }

        return view('welcome', compact('events'));
    }

    public function dashboard(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('user.dashboard');
    }
}
