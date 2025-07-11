<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Team, User, TeamMember};

class TeamManagementController extends Controller
{
    public function index()
    {
        $teams = Team::orderBy('id','desc')->get();
        $users = User::where('role', '!=', 'Super Admin')
                       ->where('role',['member'])->get();
    
        return view('admin.team-management.index',compact('teams','users'));
    }

    public function store(Request $request)
    {
        Team::UpdateOrCreate(
            ['id' => $request->team_id],
            ['name' => $request->name]
        );

        return response()->json(['status' => true, 'message' => 'Teams Added Successfully']);
    }

    public function edit($id)
    {
        $team = Team::find($id);

        if ($team) {
            return response()->json([
                'status' => true,
                'data' => $team
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Team not found'
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
        'team_id' => 'required|exists:teams,id',
        'name' => 'required|string|max:255',
        ]);

        $team = Team::find($request->team_id);
        $team->name = $request->name;
        $team->save();

        return response()->json([
            'status' => true,
            'message' => 'Team updated successfully'
        ]);
    }

    // public function assignMembers(Request $request)
    // {
    //     $request->validate([
    //         'team_id' => 'required|exists:teams,id',
    //         'member_ids' => 'required|array'
    //     ]);

    //     $team = Team::find($request->team_id);
    //     $team->members()->sync($request->member_ids);

    //     return response()->json(['message' => 'Members Assigned Successfully']);
    // }

    public function statusToggle($id)
    {
        $team = Team::find($id);
        if ($team)
        {
            $team->status = !$team->status;
            $team->save();
            return response()->json(['status' => true, 'message' => 'Status Updated Successfully']);
        }
        return response()->json(['status' => false, 'message' => 'User Not Found']);
    }


    public function delete($id)
    {
        $team = Team::find($id);

        if($team){
            $team->delete();
            return response()->json(['status' => true, 'message' => 'Team Deleted Successfully']);
        }
        return response()->json(['status' => false, 'message' => 'Team Not Found']);
    }
}
