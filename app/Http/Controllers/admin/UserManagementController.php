<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Team, Letter};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('team')
                ->where('role', '!=', 'Super Admin')
                ->orderBy('created_at', 'desc')->paginate(10);
        $teams = Team::where('status', 1)->get();
        return view('admin.user-management.index',compact('users','teams'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 1,
        ]);


        if ($request->role === 'Member' && $request->filled('team_id')) {
            DB::table('team_members')->insert([
            'team_id' => $request->team_id,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }

        return response()->json(['status' => true, 'message' => 'User Added Successfully']);
    }


    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);

       
        if ($request->role === 'Member' && $request->filled('team_id')) {
         
            DB::table('team_members')->updateOrInsert(
                ['user_id' => $user->id],
                ['team_id' => $request->team_id, 'updated_at' => now(), 'created_at' => now()]
            );
        } else {
            
            DB::table('team_members')->where('user_id', $user->id)->delete();
        }

        return response()->json(['status' => true, 'message' => 'User Updated Successfully']);
    }


    public function statusToggle($id)
    {
        $user = User::find($id);
        if ($user)
        {
            $user->status = !$user->status;
            $user->save();
            return response()->json(['status' => true, 'message' => 'Status Updated Successfully']);
        }
        return response()->json(['status' => false, 'message' => 'User Not Found']);
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User Not Found']);
        }

        $isCreator = Letter::where('created_by', $id)->exists();

        $isHandler = Letter::where('handed_over_by', $id)->exists();

        $isRecipient = Letter::where('send_to', 'member_' . $id)->exists();

        if ($isCreator || $isHandler || $isRecipient) {
            return response()->json([
                'status' => false,
                'message' => 'User cannot be deleted because they are linked to Letters.'
            ]);
        }

        $user->delete();

        return response()->json(['status' => true, 'message' => 'User Deleted Successfully']);
    }


    public function exportUsers()
    {
        $users = User::select('name', 'email', 'role', 'status')->where('role', '!=', 'Super Admin')->where('status',1)->get();
        $csvHeader = ['Name', 'Email', 'Role', 'Status'];
        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $callback = function () use ($users, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($users as $user) {
                fputcsv($file, [
                    ucwords($user->name),
                    $user->email,
                    ucfirst($user->role),
                    ucfirst($user->status),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ]);
    }

    public function importUsers(Request $request){

        $request->validate([
            'csv_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('csv_file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

       
        $header = $rows[0];
        unset($rows[0]);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            if (!isset($data['email']) || empty($data['email'])) {
                continue; 
            }

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'] ?? '',
                    'password' => Hash::make($data['password'] ?? '123456'),
                    'role' => $data['role'] ?? 'Member',
                ]
            );
        }

        return back()->with('success', 'Users imported successfully!');
    }
}
