<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Bookshelve;
use App\Models\User;
use App\Models\Office;
use App\Models\CabBooking;
use App\Models\FlightBooking;
use App\Models\TrainBooking;
use App\Models\HotelBooking;
use App\Models\CaveLocation;
use App\Models\CaveForm;
use App\Models\CaveDoc;
use Auth;
use DB;
use App\Models\{Letter, Delivery, Team};
use Carbon\Carbon;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'Member') {
            $memberKey = 'member_' . $user->id;

            $letterQuery = Letter::where('send_to', $memberKey);

            if ($request->filled('status')) {
                $letterQuery->where('status', $request->status);
            }

            $letters = $letterQuery->orderBy('created_at', 'desc')->get();

            $total = Letter::where('send_to', $memberKey)->count();
            $delivered = Letter::where('send_to', $memberKey)->where('status', 'Delivered')->count();
            $pending = $total - $delivered;

            return view('admin.member.dashboard', [
                'letters' => $letters,
                'total' => $total,
                'delivered' => $delivered,
                'pending' => $pending,
            ]);
        }

        // if (in_array($user->role, ['Receptionist', 'Peon'])) {
        //     $name = $user->name;

        //     $totalLetters = Letter::where('handed_over_by', $name)->count();
        //     $totalDelivered = Letter::where('handed_over_by', $name)->where('status', 'Delivered')->count();
        //     $todayLetters = Letter::where('handed_over_by', $name)
        //                         ->whereDate('document_date', Carbon::today())->count();

        //     return view('home', [
        //         'todayLetters' => $todayLetters,
        //         'totalLetters' => $totalLetters,
        //         'totalDelivered' => $totalDelivered,
        //     ]);
        // }

        $user = Auth::user();

        if (in_array($user->role, ['Receptionist', 'Peon'])) {
            $userId = $user->id;

            $totalLetters = Letter::where('handed_over_by', $userId)->count();
            $totalDelivered = Letter::where('handed_over_by', $userId)->where('status', 'Delivered')->count();
            $todayLetters = Letter::where('handed_over_by', $userId)
                                ->whereDate('created_at', Carbon::today())
                                ->count();

            return view('home', [
                'todayLetters' => $todayLetters,
                'totalLetters' => $totalLetters,
                'totalDelivered' => $totalDelivered,
                'systemUsers' => 0,
            ]);
        }

        // For Admin
        $todayLetters = Letter::whereDate('created_at', Carbon::today())->count();
        $totalLetters = Letter::count();
        $totalDelivered = Letter::where('status', 'Delivered')->count();
        $systemUsers = User::where('role', '!=', 'super admin')->count();
        $totalTeam = Team::count();
 
        return view('home', [
            'todayLetters' => $todayLetters,
            'totalLetters' => $totalLetters,
            'totalDelivered' => $totalDelivered,
            'systemUsers' => $systemUsers,
            'totalTeam' => $totalTeam
        ]);
    }
}
