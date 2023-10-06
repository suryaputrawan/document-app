<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DocumentController;

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

Auth::routes([
    'register' => false,
    'reset'    => false,
    'confirm'   => false,
]);

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    include 'admin.php';
});

Route::group(['middleware' => ['auth']], function () {
    Route::resource('document', DocumentController::class);

    Route::put('document/sign/{document}', [DocumentController::class, 'sign'])->name('document.sign');
});


Route::group(['prefix' => 'noble-ui-7103'], function () {
    include 'template.php';
});

//404 for undefined routes
Route::any('/{page?}', function () {
    return View::make('error.404');
})->where('page', '.*');
