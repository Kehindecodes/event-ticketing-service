<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SendEmailController;
use Illuminate\Support\Facades\Route;

Route::post("/notify", NotificationController::class);
