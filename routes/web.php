<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Facility\OfficeController;
use App\Http\Controllers\Lms\BookCategoryController;
use App\Http\Controllers\Lms\BookshelveController;
use App\Http\Controllers\Lms\BookController;
use App\Http\Controllers\Lms\LostBookController;
use App\Http\Controllers\Lms\MemberController;
use App\Http\Controllers\Lms\IssueController;
use App\Http\Controllers\Facility\CabBookingController;
use App\Http\Controllers\Facility\TrainBookingController;
use App\Http\Controllers\Facility\FlightBookingController;
use App\Http\Controllers\Facility\HotelBookingController;
use App\Http\Controllers\Facility\PropertyController;
use App\Http\Controllers\Cave\CaveFormController;
use App\Http\Controllers\Cave\CaveLocationController;
use App\Http\Controllers\Cave\CaveCategoryController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\{UserManagementController, LetterManagementController, TeamManagementController,
                                DeliveryManagementController, ReportController};




Route::get('/cache-clear', function() {
	// \Artisan::call('route:cache');
	\Artisan::call('config:cache');
	\Artisan::call('permission:cache-reset');
   //	\Artisan::call('cache:clear');
	\Artisan::call('view:clear');
	\Artisan::call('config:clear');
	\Artisan::call('view:cache');
	\Artisan::call('route:clear');
	dd('Cache cleared');
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::group(['middleware' => ['role:super-admin|lms-admin']], function() {
// Route::group(['middleware' => ['auth']], function() {

//     Route::resource('permissions', App\Http\Controllers\PermissionController::class);
//     Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);

//     Route::resource('roles', App\Http\Controllers\RoleController::class);
//     Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);
//     Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'addPermissionToRole']);
//     Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'givePermissionToRole']);

//     Route::resource('users', App\Http\Controllers\UserController::class);
//     Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy']);
    
    
//     //facility
    
//     Route::resource('offices', OfficeController::class);
//     Route::get('offices/{userId}/delete', [OfficeController::class, 'destroy']);
//     Route::get('offices/{userId}/issue/list', [OfficeController::class, 'bookIssueList']);
//     Route::get('offices/{userId}/available/list', [OfficeController::class, 'availableBookList']);
    
//     //lms
//     Route::resource('bookcategories', BookCategoryController::class);
//     Route::get('bookcategories/{userId}/delete', [BookCategoryController::class, 'destroy']);
//     Route::get('bookcategories/{userId}/status/change', [BookCategoryController::class, 'status']);
    
    
//     Route::resource('bookshelves', BookshelveController::class);
//     Route::get('bookshelves/{userId}/delete', [BookshelveController::class, 'destroy']);
//     Route::get('bookshelves/export/csv', [BookshelveController::class, 'csvExport']);
//     Route::post('bookshelves/upload/csv', [BookshelveController::class, 'csvImport']);
    
    
//     Route::resource('books', BookController::class);
//     Route::get('books/{userId}/delete', [BookController::class, 'destroy']);
//     Route::get('books/{userId}/status/change', [BookController::class, 'status']);
//     Route::get('books/export/csv', [BookController::class, 'csvExport']);
//     Route::post('books/upload/csv', [BookController::class, 'csvImport']);
//     Route::post('books/update/csv/upload', [BookController::class, 'bookUpdatedCsv']);
//     Route::get('bookshelves/list/officewise/{userId}', [BookController::class, 'bookshelveOffice']);
//     Route::get('books/{userId}/issue/list', [BookController::class, 'bookIssueList']);
//     Route::get('books/issue/list/export/csv/{id}', [BookController::class, 'bookIssuecsvExport']);
    
//     //lost books
//      Route::resource('lostbooks', LostBookController::class);
//      Route::get('lostbooks/{userId}/delete', [LostBookController::class, 'destroy']);
//      Route::get('lost/books/{userId}/status/change', [LostBookController::class, 'status']);
//      Route::get('lost/books/export/csv', [LostBookController::class, 'csvExport']);
//      Route::post('lost/books/upload/csv', [LostBookController::class, 'csvImport']);
    
//     //history
    
//     Route::get('books/{userId}/history', [BookController::class, 'bookHistory']);
//     Route::get('books/history/export/csv/{id}', [BookController::class, 'bookHistorycsvExport']);
    
    
//     Route::get('bookshelves/get/{userId}', [BookController::class, 'bookshelveDetail']);
//     //total available books per office
//     Route::get('offices/available/books/{officeId}/list', [BookController::class, 'availableBookListOffice']);
//     //total issue books per office
//     Route::get('offices/issue/books/{officeId}/list', [BookController::class, 'issueBookListOffice']);
    
//     //unreturned book list
//     Route::get('unreturned/books/list', [BookController::class, 'unreturnedBookList']);
//     Route::get('unreturned/books/export/csv', [BookController::class, 'unreturnedBookcsvExport']);
//     //bulk issue
//     Route::get('bulk-issue/books/list', [BookController::class, 'bulkissueBookList']);
//     Route::get('bulk-issue/books/export/csv', [BookController::class, 'bulkissueBookcsvExport']);
//     //test book delete
//     Route::post('test/books/delete', [BookController::class, 'testBookDelete']);
//     //member
//     Route::resource('members', MemberController::class);
//     Route::get('members/{userId}/delete', [MemberController::class, 'destroy']);
//     Route::get('members/{userId}/status/change', [MemberController::class, 'status']);
//     Route::get('members/{userId}/issue/list', [MemberController::class, 'bookIssueList']);
//     Route::get('members/issue/list/csv/export', [MemberController::class, 'bookIssueListcsvExport']);
//     Route::get('members/export/csv', [MemberController::class, 'csvExport']);
//     Route::post('members/upload/csv', [MemberController::class, 'csvImport']);
//     Route::post('/members/permissions/{id}', [MemberController::class, 'getPermissionsAndMembers'])->name('members.getPermissionsAndMembers');
//     Route::delete('/members/permissions/delete{id}', [MemberController::class, 'getPermissionsAndMembersDelete'])->name('members.getPermissionsAndMembers.delete');
//     //all issue list
//     Route::resource('issues', IssueController::class);
//     Route::get('issues/books/{userId}/status/change', [IssueController::class, 'status']);
    
//     //cab booking
//      Route::get('cab-booking/list', [CabBookingController::class, 'index']);
//      Route::get('cab-booking/export/csv', [CabBookingController::class, 'csvExport'])->name('cab-booking.export.csv');
//      Route::get('cab-booking/details/{id}', [CabBookingController::class, 'show']);
//      Route::get('cab-booking/{id}/edit', [CabBookingController::class, 'edit']);
//      Route::post('cab-booking/{id}/update', [CabBookingController::class, 'update']);
//      Route::get('cab-booking/status/change/{id}/{status}', [CabBookingController::class, 'status']);
     
//      Route::get('train-booking/list', [TrainBookingController::class, 'index']);
//      Route::get('train-booking/export/csv', [TrainBookingController::class, 'csvExport'])->name('train-booking.export.csv');
//      Route::get('train-booking/details/{id}', [TrainBookingController::class, 'show']);
//      Route::get('train-booking/{id}/edit', [TrainBookingController::class, 'edit']);
//      Route::post('train-booking/{id}/update', [TrainBookingController::class, 'update']);
//      Route::get('train-booking/status/change/{id}/{status}', [TrainBookingController::class, 'status']);
     
//      Route::get('flight-booking/list', [FlightBookingController::class, 'index']);
//      Route::get('flight-booking/export/csv', [FlightBookingController::class, 'csvExport'])->name('flight-booking.export.csv');
//      Route::get('flight-booking/details/{id}', [FlightBookingController::class, 'show']);
//      Route::get('flight-booking/{id}/edit', [FlightBookingController::class, 'edit']);
//      Route::post('flight-booking/{id}/update', [FlightBookingController::class, 'update']);
//      Route::get('flight-booking/status/change/{id}/{status}', [FlightBookingController::class, 'status']);
     
//      Route::get('hotel-booking/list', [HotelBookingController::class, 'index']);
//      Route::get('hotel-booking/export/csv', [HotelBookingController::class, 'csvExport'])->name('hotel-booking.export.csv');
//      Route::get('hotel-booking/details/{id}', [HotelBookingController::class, 'show']);
//      Route::get('hotel-booking/{id}/edit', [HotelBookingController::class, 'edit']);
//      Route::post('hotel-booking/{id}/update', [HotelBookingController::class, 'update']);
//      Route::get('hotel-booking/status/change/{id}/{status}', [HotelBookingController::class, 'status']);
     
//      Route::get('edit-logs/list', [HotelBookingController::class, 'editLogs']);
//      Route::get('edit-logs/export/csv', [HotelBookingController::class, 'editLogscsvExport'])->name('edit-logs.export.csv');
     
//      Route::resource('properties', PropertyController::class);
     
//      //cave
//      Route::resource('vaults', CaveFormController::class);
//      Route::get('vaults/export/csv', [CaveFormController::class, 'csvExport'])->name('vaults.export.csv');
//       //unreturned book list
//         Route::get('outside/vault/list', [CaveFormController::class, 'unreturnedVaultList'])->name('outside.vault.list');
//         Route::get('outside/vault/export/csv', [CaveFormController::class, 'unreturnedVaultListcsvExport']);
//      Route::get('vaults/{id}/delete', [CaveFormController::class, 'destroy'])->name('vaults.delete');
//       Route::get('vaults/{userId}/takeout/list', [CaveFormController::class, 'takeoutList']);
//       Route::get('vaults/takeout/list/export/csv/{id}', [CaveFormController::class, 'takeoutListcsvExport']);
      
//      Route::resource('vaultlocations', CaveLocationController::class);
//      Route::get('vaultlocations/{id}/delete', [CaveLocationController::class, 'destroy'])->name('vaultlocations.delete');
     
//      Route::resource('vaultcategories', CaveCategoryController::class);
//      Route::get('vaultcategories/{id}/delete', [CaveCategoryController::class, 'destroy'])->name('vaultcategories.delete');
//      Route::get('room/list/locationwise/{id}', [CaveCategoryController::class, 'roomList'])->name('room.list');
     

// });

Route::middleware(['auth', 'role:super admin'])->group(function () {
    //user management
    Route::prefix('user-management')->group(function() {
        Route::get('/', [UserManagementController::class, 'index'])->name('admin.user.management');
        Route::post('/store',[UserManagementController::class, 'store'])->name('admin.user.store');
        Route::post('/update/{id}',[UserManagementController::class, 'update'])->name('admin.user.update');
        Route::post('/status-toggle/{id}',[UserManagementController::class, 'statusToggle'])->name('admin.user.toggle');
        Route::post('/delete/{id}',[UserManagementController::class, 'delete'])->name('admin.user.delete');
        Route::get('/export-users', [UserManagementController::class, 'exportUsers'])->name('admin.user.export');
        Route::post('/import-users', [UserManagementController::class, 'importUsers'])->name('admin.user.import');
    });
    
    //letter management
    Route::prefix('letter-management')->group(function() {
        Route::get('/', [LetterManagementController::class, 'index'])->name('admin.letter.management');
        Route::post('/store', [LetterManagementController::class, 'store'])->name('admin.letter.store');
        Route::get('/edit/{id}', [LetterManagementController::class, 'edit'])->name('admin.letter.edit');
        Route::post('/update/{id}', [LetterManagementController::class, 'update'])->name('admin.letter.update');
        Route::post('/delete/{id}',[LetterManagementController::class, 'delete'])->name('admin.letter.delete');
        Route::get('/export-letters', [LetterManagementController::class, 'exportLetters'])->name('admin.letter.export');

    });

    Route::prefix('team-managenment')->group(function() {
        Route::get('/', [TeamManagementController::class, 'index'])->name('admin.team.management');
        Route::post('/store', [TeamManagementController::class, 'store'])->name('admin.team.store');
        Route::get('/edit/{id}', [TeamManagementController::class, 'edit'])->name('admin.team.edit');
        Route::post('/update', [TeamManagementController::class, 'update'])->name('admin.team.update');
       // Route::post('teams/assign', [TeamManagementController::class, 'assignMembers'])->name('admin.team.assignmembers');
        Route::post('/status-toggle/{id}',[TeamManagementController::class, 'statusToggle'])->name('admin.team.toggle');
        Route::post('/delete/{id}',[TeamManagementController::class, 'delete'])->name('admin.team.delete');
    });

    Route::prefix('delivery-management')->group(function () {
        Route::get('/', [DeliveryManagementController::class, 'index'])->name('admin.delivery.index'); 
        Route::post('/letters/confirm-delivery', [DeliveryManagementController::class, 'confirmDelivery'])->name('admin.delivery.confirm'); 
        Route::get('/letters/download/{id}',[DeliveryManagementController::class, 'downloadReport'])->name('admin.delivery.download');
        Route::get('/delivery/report/{id}',[DeliveryManagementController::class, 'deliveryReportPdf'])->name('admin.delivery.report');
    });

    Route::prefix('Report')->group(function (){
        Route::get('/', [ReportController::class, 'index'])->name('admin.report.index');
    });
});

Route::middleware(['auth', 'role:Receptionist,super admin'])->group(function () {
    // Letter Management
    Route::prefix('letter-management')->group(function() {
        Route::get('/', [LetterManagementController::class, 'index'])->name('admin.letter.management');
        Route::post('/store', [LetterManagementController::class, 'store'])->name('admin.letter.store');
        Route::get('/edit/{id}', [LetterManagementController::class, 'edit'])->name('admin.letter.edit');
        Route::post('/update/{id}', [LetterManagementController::class, 'update'])->name('admin.letter.update');
        Route::post('/delete/{id}',[LetterManagementController::class, 'delete'])->name('admin.letter.delete');
        Route::get('/export-letters', [LetterManagementController::class, 'exportLetters'])->name('admin.letter.export');

    });
});

Route::middleware(['auth', 'role:Receptionist,super admin,Peon,Member'])->group(function () {
    Route::prefix('delivery-management')->group(function () {
        Route::get('/', [DeliveryManagementController::class, 'index'])->name('admin.delivery.index');
        Route::post('/letters/confirm-delivery', [DeliveryManagementController::class, 'confirmDelivery'])->name('admin.delivery.confirm');
        Route::get('/letters/download/{id}', [DeliveryManagementController::class, 'downloadReport'])->name('admin.delivery.download');
        Route::get('/delivery/report/{id}', [DeliveryManagementController::class, 'deliveryReportPdf'])->name('admin.delivery.report');
    });
});

Route::middleware(['auth', 'role:Receptionist,super admin'])->group(function (){
    Route::prefix('Report')->group(function (){
        Route::get('/', [ReportController::class, 'index'])->name('admin.report.index');
    });
});



