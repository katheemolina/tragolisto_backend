<?php

use App\Http\Controllers\LoginGoogleController;
use App\Http\Controllers\TragosController;
use App\Http\Controllers\FerniController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/ferni', [FerniController::class, 'old_responder']);

// Las rutas de tragos están definidas en web.php para mantener consistencia con Lumen

Route::post('/login-google', [LoginGoogleController::class, 'login']);


Route::post('/verificar-onboarding', [UserController::class, 'verificarOnboarding']);
Route::post('/completar-onboarding', [UserController::class, 'completarOnboarding']);
