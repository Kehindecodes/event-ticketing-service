<?php

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Route;
use Termwind\Components\Hr;

Route::post('/notify', function () {
    NotificationService::send(
        NotificationType::TICKET_OFFERED,
        'Hello, World!',
        ''
    );
});
