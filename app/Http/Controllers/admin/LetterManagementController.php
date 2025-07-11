<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\{Letter, User, Team};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LetterManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); 

        $lettersQuery = Letter::with('handedOverByUser')->orderBy('id', 'desc');

        if ($user->role === 'Receptionist') {
            $lettersQuery->where('created_by', $user->id);
        }

        // if ($request->filled('from_date')) {
        //     // Parse the 'from_date' and set it to the beginning of the day (00:00:00)
        //     $fromDate = Carbon::parse($request->from_date)->startOfDay();
        //     $lettersQuery->where('created_at', '>=', $fromDate);
        // }

        // if ($request->filled('to_date')) {
        //     // Parse the 'to_date' and set it to the end of the day (23:59:59)
        //     $toDate = Carbon::parse($request->to_date)->endOfDay();
        //     $lettersQuery->where('created_at', '<=', $toDate);
        // }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();

            $lettersQuery->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('status')) {
            $lettersQuery->where('status', $request->status);
        }

        //dd($lettersQuery->toSql(), $lettersQuery->getBindings());
        $letters = $lettersQuery->paginate(10);

        $members = User::where('role', 'Member')->with('team')->get();
        $teams = Team::all();
        $users = User::whereIn('role', ['Peon', 'Receptionist'])->get();

        return view('admin.letter-management.index', compact('letters', 'members', 'users', 'teams'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'received_from' => 'nullable|string',
            'handed_over_by' => 'nullable',
            'send_to' => 'nullable|string',
            'subject' => 'nullable|string',
            'document_reference_no' => 'nullable|string',
            'document_date' => 'nullable|date',
            'document_image' => 'nullable|file|mimes:jpg,png,webp,jpeg,gif,pdf|max:5120',
        ]);

        $data = $request->except(['_token']);

        if ($request->document_image) {
            $file = $request->file('document_image');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/letters'), $filename);
            $data['document_image'] = $filename;
        }
        $data['created_by'] = Auth::id();
        $letter = Letter::create($data);

        if (!$letter) {
            return response()->json(['status' => false, 'message' => 'Failed to add letter.']);
        }
      
        $letter->letter_id = 'LT' . str_pad($letter->id, 6, '0', STR_PAD_LEFT);
        $letter->save();

        return response()->json(['status' => true, 'message' => 'Letter Added Successfully']);
    }


    public function edit($id)
    {
        $letter = Letter::findOrFail($id);
        return response()->json(['status' => true, 'data' => $letter]);
    }


    public function update(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        $request->validate([
            'received_from' => 'nullable|string',
            'handed_over_by' => 'nullable',
            'send_to' => 'nullable|string',
            'subject' => 'nullable|string',
            'document_reference_no' => 'nullable|string',
            'document_date' => 'nullable|date',
            'document_image' => 'nullable|file|mimes:jpg,png,webp,jpeg,gif,pdf|max:5120',
        ]);

        try {
            $data = $request->except(['letter_id', '_token']);
            
            if ($request->document_image) {
                $file = $request->file('document_image');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/letters'), $fileName);
                $data['document_image'] = $fileName;
            }

            $letter->update($data);

            return response()->json(['status' => true, 'message' => 'Letter Updated Successfully']);
            } catch (\Exception $e) {
                \Log::error('Letter update failed: ' . $e->getMessage());
                return response()->json(['status' => false, 'message' => 'Update failed: ' . $e->getMessage()], 500);
                
        }
            
    }


    public function delete($id){
        $letter = Letter::findOrFail($id);
        if ($letter->document_image && file_exists(public_path('uploads/letters/' . $letter->document_image))) {
            unlink(public_path('uploads/letters/' . $letter->document_image));
        }
        $letter->delete();
        return response()->json(['status' => true, 'message' => 'Letter Deleted Successfully']);
    }


    public function exportLetters(Request $request)
    {
        $user = Auth::user();

        $lettersQuery = Letter::with('handedOverByUser');

        if ($user->role === 'Receptionist') {
            $lettersQuery->where('created_by', $user->id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $lettersQuery->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('status')) {
            $lettersQuery->where('status', $request->status);
        }

        $letters = $lettersQuery->get();

        $csvHeader = [
            'Letter ID',
            'Received From',
            'Handed Over By',
            'Send To', 
            'Subject',
            'Document Ref No',
            'Document Date',
            'Status',
            'created_at'
        ];

        $filename = 'letters_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $callback = function () use ($letters, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($letters as $letter) {
                $sendTo = $letter->send_to;
                $sendToName = '';

                if (Str::startsWith($sendTo, 'member_')) {
                    $memberId = Str::after($sendTo, 'member_');
                    $member = User::find($memberId);
                    $sendToName = $member ? ucwords($member->name) . ' (Member)' : 'Unknown Member';
                } elseif (Str::startsWith($sendTo, 'team_')) {
                    $teamId = Str::after($sendTo, 'team_');
                    $team = Team::find($teamId); 
                    $sendToName = $team ? ucwords($team->name) : 'Unknown Team';
                } else {
                    $sendToName = ucwords($sendTo);
                }

                fputcsv($file, [
                    $letter->letter_id,
                    ucwords($letter->received_from),
                    ucwords(optional($letter->handedOverByUser)->name ?? 'N/A'),
                    $sendToName,
                    ucwords($letter->subject),
                    $letter->document_reference_no,
                    $letter->document_date,
                    ucfirst($letter->status),
                    $letter->created_at ? $letter->created_at->format('d-m-Y') : '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ]);
    }
}
