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

    Route::resource('clientes', ClienteController::class);
    Route::resource('produtos', ProdutoController::class);
    Route::resource('vendas', VendaController::class);
    Route::resource('itens_venda', ItensVendaController::class);



    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
