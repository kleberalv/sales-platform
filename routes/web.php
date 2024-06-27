<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\ItensVendaController;

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

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::get('admin', function () {
        return view('admin');
    })->name('admin');

    Route::resource('admin/clientes', ClienteController::class);
    Route::resource('admin/produtos', ProdutoController::class);
    Route::resource('admin/vendas', VendaController::class);
    Route::get('admin/relatorio', [VendaController::class, 'relatorio'])->name('vendas.relatorio');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
