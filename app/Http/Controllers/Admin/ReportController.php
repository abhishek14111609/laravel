<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->date('from');
        $to = $request->date('to');

        $payments = Payment::query()->where('status', '=', 'paid');
        $bookings = Booking::query();

        if ($from) {
            $payments->whereDate('created_at', '>=', $from, 'and');
            $bookings->whereDate('created_at', '>=', $from, 'and');
        }

        if ($to) {
            $payments->whereDate('created_at', '<=', $to, 'and');
            $bookings->whereDate('created_at', '<=', $to, 'and');
        }

        $revenue = $payments->sum('amount');
        $bookingStatusSummary = $bookings
            ->selectRaw('status, COUNT(*) as total', [])
            ->groupBy('status')
            ->pluck('total', 'status');

        $dailyRevenue = Payment::query()
            ->where('status', '=', 'paid')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from, 'and'))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to, 'and'))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total', [])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.reports.index', compact('revenue', 'bookingStatusSummary', 'dailyRevenue', 'from', 'to'));
    }
}
