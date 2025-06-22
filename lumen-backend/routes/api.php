<?php

use App\Http\Controllers\LoginGoogleController;
use App\Http\Controllers\TragosController;
use App\Http\Controllers\FerniController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/ferni', [FerniController::class, 'old_responder']);

Route::get('/tragos', [TragosController::class, 'getTragos']);
Route::get('/tragos/{id}', [TragosController::class, 'getTragoPorID']);
Route::get('/tragos', [TragosController::class, 'getTragosPorIngredientes']);

Route::post('/login-google', [LoginGoogleController::class, 'login']);


Route::post('/verificar-onboarding', [UserController::class, 'verificarOnboarding']);
Route::post('/completar-onboarding', [UserController::class, 'completarOnboarding']);
