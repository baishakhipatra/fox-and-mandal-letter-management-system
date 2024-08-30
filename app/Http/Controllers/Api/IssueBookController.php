<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IssueBook;
use App\Models\Book;
use Illuminate\Validation\ValidationException;

class IssueBookController extends Controller
{
    public function store(Request $request)
    {

        // $validated = $request->validate([
        //     'book_id' => 'required|array',
        //     'request_date' => 'required|date',
        //     'approve_date' => 'nullable|date',
        // ]);

        // $bookIds = $request->book_id; 
        // foreach ($bookIds as $bookId) {
        // $data = IssueBook::create([
        //         'user_id' => $request->user_id,
        //         'book_id' => $bookId,
        //         'request_date' => $request->request_date,
        //         'status' => $request->status,
        //         // Uncomment and use the status field if needed
        //         // 'status' => $validated['status'],
        //         'approve_date' => $request->approve_date,
        //     ]);
        //     $insertedData[] = $data;
        // }

  
        // return response()->json([
        //     'message' => 'Books issued successfully.',
        //    'data'=>$insertedData
        // ]);


        // Validate the input data
        $validated = $request->validate([
            'book_id' => 'required|array',
            'user_id' => 'required', 
            'request_date' => 'required',
            // 'approve_date' => 'nullable',
        ]);

        $insertedData = [];

        try {
            
            foreach ($validated['book_id'] as $bookId) {
                $data = IssueBook::create([
                    'user_id' => $validated['user_id'],
                    'book_id' => $bookId,
                    'request_date' => $validated['request_date'],
                    // 'approve_date' => $validated['approve_date'],
                ]);
                $insertedData[] = $data;
            }

            return response()->json([
                'message' => 'Books issued successfully.',
                'data' => $insertedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while issuing books.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }
    public function listByUser(Request $request)
    {
  
        $userId = $request->input('user_id');

        $books = IssueBook::where('user_id', $userId)
            ->get();

       
        return response()->json([
                                    'message' => 'List of book of user',
                                    'data' =>$books
                                ], 200);
    }
    
    
}
