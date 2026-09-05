<?php

use Illuminate\Support\Facades\Route;
use Termwind\Components\Hr;

Route::get('/', function () {
    return response('Hello, World!', 200);
});
