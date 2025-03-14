<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ParkingController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\AdminController;
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
Route::post('/auth/register', [LoginController::class, 'createUser']);
Route::post('/auth/login', [LoginController::class, 'loginUser']);

    Route::post('/initialiserplaces/{parkingId}', [ParkingController::class, 'initialiserPlaces']);
Route::get('/parking/search',[ParkingController::class,'searshParking']);
Route::post('/parking/store',[ParkingController::class,'store']);
Route::put('/parking/modifier/{id}', [ParkingController::class, 'update']);
Route::delete('/parking/supprimer', [ParkingController::class, 'destroy']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reservation', [ReservationController::class, 'reserverPlace']);
    Route::delete('/annulereservation/{id}', [ReservationController::class, 'annulerReservation']);
    route::get('/afficherreservation',[ReservationController::class,'afficherReservationsUtilisateur']);
    Route::put('/modifierreservation/{id}', [ReservationController::class, 'modifierReservation']);

});
route::post('/admin/parking',[AdminController::class,'store']);
route::put('/admin/modifier/parking/{id}',[AdminController::class,'update']);
route::delete('admin/supprimer/parking',[AdminController::class,'destroy']);
route:: get('/admin/statistique',[AdminController::class,'statistique']);