<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    

    public function search(Request $request)
    {
  
        $query = $request->input('query');

        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->orWhere('publisher', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->orWhere('uid', 'LIKE', "%{$query}%")
            ->get();

        // return response()->json($books);
        return response()->json(['message' => 'List of search data',$books], 200);
    }

}
