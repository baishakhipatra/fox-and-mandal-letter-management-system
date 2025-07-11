<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{User, Delivery, Letter};
use Carbon\carbon;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        $period = $request->input('period');
        $userId = $request->input('user_id');
        $status = $request->input('status'); 
        $users = User::where('role', '!=', 'super admin')->get(['id', 'name', 'role']);

        $performanceData = [];

        if ($userId) {
            $user = User::find($userId);

            $assigned = Letter::where('send_to', $userId)
                ->when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
                ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
                ->count();

            $delivered = Delivery::where('delivered_to_user_id', $userId)
                ->when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
                ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
                ->count();

            $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 2) : 0;

            $performanceData = [
                'name' => $user->name,
                'role' => $user->role,
                'assigned' => $assigned,
                'delivered' => $delivered,
                'rate' => $rate,
            ];
        } elseif ($period) {
          
            $assigned = Letter::when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
                ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
                ->count();

            $delivered = Delivery::when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
                ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
                ->count();

            $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 2) : 0;

            $performanceData = [
                'name' => 'All Users',
                'role' => '-',
                'assigned' => $assigned,
                'delivered' => $delivered,
                'rate' => $rate,
            ];
        } else {
            $assigned = Letter::count();
            $delivered = Delivery::count();
            $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 2) : 0;

            $performanceData = [
                'name' => 'All Users',
                'role' => '-',
                'assigned' => $assigned,
                'delivered' => $delivered,
                'rate' => $rate,
            ];
        }

        return view('admin.report.index', compact('users', 'performanceData'));
    }

}
