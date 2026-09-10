<?php

use App\Http\Controllers\NotificationContoller;
use Illuminate\Support\Facades\Route;

Route::post('/notify', [NotificationContoller::class, '__invoke']);
