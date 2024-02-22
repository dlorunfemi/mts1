<?php

use App\Http\Controllers\ProfileController;
use App\Models\Tenant;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/dashboard', function () {
    // dd(auth()->user()->tenants);
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('tenants/change/{tenant}', function(Tenant $tenant) {
        auth()->user()->update(['tenant_id' => $tenant->id]);

        // TODO: create a global helper function that  returns this route name?
        $tenantDomain = str_replace('://', '://' . $tenant->domain . '.', config('app.url'));

        return redirect($tenantDomain . RouteServiceProvider::HOME)->with('status', 'Your account has been switched! You are currently using ' . $tenant->name );
    })->name('tenants.switch');
});

require __DIR__.'/auth.php';
