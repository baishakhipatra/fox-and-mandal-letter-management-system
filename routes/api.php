<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\IssueBookController;
use App\Http\Controllers\Api\BookShelveController;
use App\Http\Controllers\Api\BookmarkController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('login', [AuthController::class, 'sendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::get('/books/search', [BookController::class, 'search']);
Route::get('/books/list', [BookController::class, 'index']);
Route::get('/books/details-by-qrcode', [BookController::class, 'searchDetailsByQrCode']);
Route::get('/books/category-wise-list', [BookController::class, 'CategoryWiseBookList']);


Route::post('/issue-books', [IssueBookController::class, 'store']);

Route::get('/issue-books/list-by-user', [IssueBookController::class, 'listByUser']);
Route::get('/books-shelve/search-by-qrcode', [BookShelveController::class, 'searchByQrCode']);
Route::post('/bookmark', [BookmarkController::class, 'store']);
Route::get('/bookmark/list', [BookmarkController::class, 'index']);

Route::get('/books/detai-by-book-shelves-qrcode', [BookController::class, 'showBooksByBookShelveQRCode']);


// 1.Login with phone number (Api create)-1hr
// 2.Otp Verification for login(Api create)->1hr
// 3.Search Api for book(Api create)->2hr
// 4.List Api for book(Api create)->30min
// 5.Issue Book(Api create)(Continue)


// 1.Book details by QR-code 
// 2.Issue book api
// 3.Issue book list user 
// 4.book shelves searce by qr code 
// 5.Book Bookmark 
// 6. Bookmark list 
// 7.Book details by book shelves qr code