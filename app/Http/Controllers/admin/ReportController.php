<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{User, Delivery, Letter};
use Carbon\carbon;

class ReportController extends Controller
{

    // public function index(Request $request)
    // {
    //     $period = $request->input('period');
    //     $userId = $request->input('user_id');
    //     $status = $request->input('status'); 
    //     $users = User::where('role', '!=', 'super admin')->get(['id', 'name', 'role']);

    //     $performanceData = [];

    //     if ($userId) {
    //         $user = User::find($userId);

    //         $assigned = Letter::where('send_to', $userId)
    //             ->when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
    //             ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
    //             ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
    //             ->count();

    //         $delivered = Delivery::where('delivered_to_user_id', $userId)
    //             ->when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
    //             ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
    //             ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
    //             ->count();

    //         $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 2) : 0;

    //         $performanceData = [
    //             'name' => $user->name,
    //             'role' => $user->role,
    //             'assigned' => $assigned,
    //             'delivered' => $delivered,
    //             'rate' => $rate,
    //         ];
    //     } elseif ($period) {
          
    //         $assigned = Letter::when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
    //             ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
    //             ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
    //             ->count();

    //         $delivered = Delivery::when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
    //             ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
    //             ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month))
    //             ->count();

    //         $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 2) : 0;

    //         $performanceData = [
    //             'name' => 'All Users',
    //             'role' => '-',
    //             'assigned' => $assigned,
    //             'delivered' => $delivered,
    //             'rate' => $rate,
    //         ];
    //     } else {
    //         $assigned = Letter::count();
    //         $delivered = Delivery::count();
    //         $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 2) : 0;

    //         $performanceData = [
    //             'name' => 'All Users',
    //             'role' => '-',
    //             'assigned' => $assigned,
    //             'delivered' => $delivered,
    //             'rate' => $rate,
    //         ];
    //     }

    //     return view('admin.report.index', compact('users', 'performanceData'));
    // }

    public function index(Request $request)
    {
        $period = $request->input('period');
        $status = $request->input('status');
        $userId = $request->input('user_id');

        $authenticatedUser = Auth::user();

        
        if ($authenticatedUser->role === 'Receptionist') {
            $userId = $authenticatedUser->id;
        }

      
        $users = collect(); 
        if ($authenticatedUser->role === 'super admin') {
            $users = User::where('role', '!=', 'super admin')->where('status',1)->get(['id', 'name', 'role']);
        }

        $performanceData = [];
        $finalAssignedCount = 0;
        $finalDeliveredCount = 0;

        $reportUser = $userId ? User::find($userId) : null;

        $assignedQuery = Letter::query();
        $deliveredQuery = Delivery::query();

        if ($reportUser) {
            $sendToValue = $reportUser->role . '_' . $reportUser->id;

            $assignedQuery->whereRaw('LOWER(send_to) = ?', [strtolower($sendToValue)]);
            $deliveredQuery->where('delivered_to_user_id', $reportUser->id);

            if ($authenticatedUser->role === 'Receptionist') {
                $deliveredLetterIds = Delivery::where('delivered_to_user_id', $reportUser->id)->pluck('letter_id');

                if ($status === 'delivered') {
                    $assignedQuery->whereIn('id', $deliveredLetterIds);
                }

                if ($status === 'pending') {
                    $assignedQuery->whereNotIn('id', $deliveredLetterIds);
                }
            }

            $performanceName = $reportUser->name;
            $performanceRole = $reportUser->role;
        }else {
            $performanceName = 'All Users';
            $performanceRole = '-';
        }

        $assignedQuery->when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
                    ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month));

        $deliveredQuery->when($period == 'today', fn($q) => $q->whereDate('created_at', today()))
                    ->when($period == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->when($period == 'month', fn($q) => $q->whereMonth('created_at', now()->month));

        $finalAssignedCount = $assignedQuery->count();
        $finalDeliveredCount = $deliveredQuery->count();

        $rate = $finalAssignedCount > 0 ? round(($finalDeliveredCount / $finalAssignedCount) * 100, 2) : 0;

        $performanceData = [
            'name' => $performanceName,
            'role' => $performanceRole,
            'assigned' => $finalAssignedCount,
            'delivered' => $finalDeliveredCount,
            'rate' => $rate,
        ];

        return view('admin.report.index', compact('users', 'performanceData'));
    }

    public function reportExport(Request $request)
    {
        $period = $request->input('period');
        $status = $request->input('status');
        $userId = $request->input('user_id');

        $authUser = auth()->user();

        if ($authUser->role === 'Receptionist') {
            $userId = $authUser->id;
        }

        $reportUser = $userId ? User::find($userId) : null;

        $performanceName = $reportUser ? $reportUser->name : 'All Users';
        $performanceRole = $reportUser ? $reportUser->role : '-';

        $assignedQuery = Letter::query();
        $deliveredQuery = Delivery::query();

        if ($reportUser) {
            $assignedQuery->where('send_to', $reportUser->id);
            $deliveredQuery->where('delivered_to_user_id', $reportUser->id);
        }

        if ($period === 'today') {
            $assignedQuery->whereDate('created_at', today());
            $deliveredQuery->whereDate('created_at', today());
        } elseif ($period === 'week') {
            $assignedQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            $deliveredQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $assignedQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $deliveredQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        if ($status) {
            $assignedQuery->whereRaw('LOWER(status) = ?', [strtolower($status)]);
        }

        $finalAssignedCount = $assignedQuery->count();
        $finalDeliveredCount = $deliveredQuery->count();

        $rate = $finalAssignedCount > 0 ? round(($finalDeliveredCount / $finalAssignedCount) * 100, 2) : 0;

        // CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_performance_report.csv"',
        ];

        $callback = function () use ($period, $performanceName, $performanceRole, $finalAssignedCount, $finalDeliveredCount, $rate) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Time Period', 'User Name', 'Assigned', 'Delivered', 'Rate (%)']);

            // Data
            fputcsv($file, [
                ucfirst($period) ?: 'All Time',
                ucwords($performanceName) . ' (' . ucfirst($performanceRole) . ')',
                $finalAssignedCount,
                $finalDeliveredCount,
                $rate . '%',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }




}
