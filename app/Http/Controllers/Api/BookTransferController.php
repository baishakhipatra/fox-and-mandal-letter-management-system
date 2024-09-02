<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookTransfer; // Ensure this is the correct model
use Illuminate\Validation\ValidationException;

class BookTransferController extends Controller
{
    public function transferBook(Request $request)
    {
        // try {
            $validated = $request->validate([
                'book_id' => 'required|exists:books,id', 
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',  
            ]);

            $data = BookTransfer::create([
                'book_id' => $validated['book_id'], 
                'is_transfer' => 1,
                'from_user_id' => $validated['from_user_id'],
                'to_user_id' => $validated['to_user_id'],
                'transfer_date' => now()->toDateString(),
            ]);

            return response()->json([
                'message' => 'Book transfer status updated successfully.',
                'data' => $data
            ], 201); 
        // } catch (ValidationException $e) {
            
        //     return response()->json([
        //         'message' => 'Validation error',
        //         'errors' => $e->errors()
        //     ], 422); 
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => 'An error occurred',
        //         'error' => $e->getMessage()
        //     ], 500); 
        // }
    }
}
