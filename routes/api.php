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


Route::post('/issue-books', [IssueBookController::class, 'store']);

Route::get('/issue-books/list-by-user', [IssueBookController::class, 'listByUser']);
Route::get('/books-shelve/search-by-qrcode', [BookShelveController::class, 'searchByQrCode']);
Route::post('/bookmark', [BookmarkController::class, 'store']);
Route::get('/bookmark/list', [BookmarkController::class, 'index']);

