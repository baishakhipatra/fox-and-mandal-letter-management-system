<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\{Letter,User,Delivery, Team};
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class DeliveryManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $lettersQuery = Letter::with(['delivery.deliveredToUser.team']);

        
        if ($user->role === 'Peon' || $user->role === 'Receptionist') {
            $lettersQuery->where(function ($query) use ($user) {
                $query->where('handed_over_by', $user->id)
                    ->orWhereHas('delivery', function ($q) use ($user) {
                        $q->where('delivered_to_user_id', $user->id);
                    });
            });
        }

        if ($request->filled('status')) {
            $lettersQuery->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $lettersQuery->where('handed_over_by', $request->user_id);
        }

        if ($request->filled('document_type')) {
            $lettersQuery->where('document_type', $request->document_type);
        }

        if ($request->filled('created_at')) {
            $lettersQuery->whereDate('created_at', $request->created_at);
        }

        $letters = $lettersQuery->with('handedOverByUser')->orderBy('created_at', 'desc')->paginate(10);

        $users = User::with('team')->where('role', '!=', 'Super Admin')->get();
        $members = User::where('role', 'member')->with('team:id,name')->get(['id', 'name']);

        $letterTeamMap = [];
        foreach ($letters as $letter) {
            if (Str::startsWith($letter->send_to, 'team_')) {
                $teamId = Str::after($letter->send_to, 'team_');
                $letterTeamMap[$letter->id] = (int) $teamId;
            }
        }

        return view('admin.delivery-management.index', compact('letters', 'members','users', 'letterTeamMap'));
    }

    public function confirmDelivery(Request $request)
    {
        $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'delivered_to_user_id' => 'required|exists:users,id', 
            'signature_data' => 'required|string',
        ], [
            'delivered_to_user_id.required' => 'Please select a member to deliver to.',
            'delivered_to_user_id.exists' => 'The selected member does not exist.',
            'signature_data.required' => 'Signature is required.',
        ]);

        try {
            $letter = Letter::findOrFail($request->letter_id);

            $signatureData = $request->signature_data;
            $base64Image = Str::after($signatureData, 'data:image/png;base64,');
            $decodedImage = base64_decode($base64Image);

            $imageName = 'signatures/' . Str::uuid() . '.png'; 
            Storage::disk('public')->put($imageName, $decodedImage);

         
            $letter->status = 'Delivered';
            $letter->save();

            Delivery::create([
                'letter_id' => $letter->id,
                'delivered_to_user_id' => $request->delivered_to_user_id,
                'signature_image_path' => Storage::url($imageName), 
                'delivered_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Letter delivered successfully.']);

        } catch (\Exception $e) {
            \Log::error('Delivery confirmation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error confirming delivery: ' . $e->getMessage()], 500);
        }
    }

    public function downloadReport($id){
        $letter = Letter::with('delivery.deliveredToUser.team')->findOrFail($id);
        $pdf = Pdf::loadView('admin.delivery-management.pdf', compact('letter'));
        return $pdf->download("letter-report-{$letter->letter_id}.pdf");
    }

    public function deliveryReportPdf($id)
    {
        $letter = Letter::with('delivery.deliveredToUser.team')->findOrFail($id);
        $pdf = Pdf::loadView('admin.delivery-management.delivery_report', compact('letter'));
        return $pdf->download("delivery-confirmation-{$letter->letter_id}.pdf");
    }

    public function getTeamMembers($teamId)
    {
       // $team = Team::with('members.team')->find($teamId);
        $team = Team::with(['members' => function($query) {
            $query->where('status', 1);
        }, 'members.team'])->find($teamId);

        if (!$team) {
            return response()->json(['members' => []]);
        }

        $members = $team->members->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => ucwords($member->name),
                'teams' => $member->team->pluck('name')->toArray(),
            ];
        });

        return response()->json(['members' => $members]);
    }

    public function getAllMembers()
    {
        $members = User::where('role', 'member')->where('status',1)->with('team')->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => ucwords($member->name),
                'teams' => $member->team->pluck('name')->toArray(),
            ];
        });

        return response()->json(['members' => $members]);
    }

}
