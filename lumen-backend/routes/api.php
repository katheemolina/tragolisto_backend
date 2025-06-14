<?php

use App\Http\Controllers\LoginGoogleController;
use App\Http\Controllers\TragosController;
use App\Http\Controllers\FerniController;

Route::post('/ferni', [FerniController::class, 'responder']);

Route::get('/tragos', [TragosController::class, 'getTragos']);
Route::get('/tragos/{id}', [TragosController::class, 'getTragoPorID']);
Route::get('/tragos', [TragosController::class, 'getTragosPorIngredientes']);

Route::post('/login-google', [LoginGoogleController::class, 'login']);