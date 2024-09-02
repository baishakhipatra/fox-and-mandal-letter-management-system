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
        try {
            // Validate incoming request data
            $validated = $request->validate([
                'book_id' => 'required|exists:books,id', // Ensure book_id exists in books table
                'from_user_id' => 'required|exists:users,id', // Ensure from_user_id exists in users table
                'to_user_id' => 'required|exists:users,id',   // Ensure to_user_id exists in users table
            ]);

            // Create a new book transfer record
            $data = BookTransfer::create([
                'book_id' => $validated['book_id'], // Assuming you want to track which book is being transferred
                'is_transfer' => 1,
                'from_user_id' => $validated['from_user_id'],
                'to_user_id' => $validated['to_user_id'],
                'transfer_date' => now()->toDateString(),
            ]);

            // Return a successful response with the transfer record
            return response()->json([
                'message' => 'Book transfer status updated successfully.',
                'data' => $data
            ], 201); // 201 Created status code
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity status code
        } catch (\Exception $e) {
            // Handle other errors
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error status code
        }
    }
}
