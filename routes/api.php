<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\userController;
use Illuminate\Support\Facades\Route;

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



// Route::middleware('auth:api')->post('/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/role', [RoleController::class, 'store']);
Route::post('/user', [userController::class, 'store']);
Route::get('/user', [userController::class, 'index']);

Route::prefix('v1')->group(function () {
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store','show']);
    Route::get('clients/{id}/user', [ClientController::class, 'get'])->name('articlesWithUser');
    

});

// Route group for articles management
Route::prefix('articles')->group(function () {
    // GET /articles - Display a listing of the articles
    Route::get('/', [ArticlesController::class, 'index'])->name('articles.index');

    // POST /articles - Store a newly created article
    Route::post('/', [ArticlesController::class, 'store'])->name('articles.store');

    // GET /articles/{id} - Display a specific article
    Route::get('/{id}', [ArticlesController::class, 'show'])->name('articles.show');

    // PUT /articles/{id} - Update the specified article
    Route::put('/{id}', [ArticlesController::class, 'update'])->name('articles.update');

    // PATCH /articles/mass-update - Mass update articles
    Route::post('/Articles/stock', [ArticlesController::class, 'massUpdate'])->name('articles.massUpdate');

    // DELETE /articles/{id} - Remove the specified article
    Route::delete('/{id}', [ArticlesController::class, 'destroy'])->name('articles.destroy');
});
