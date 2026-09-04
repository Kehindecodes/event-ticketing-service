<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    #[Fillable(
     'user_id',
     'notification_type',
     'status',
     'message',
    )]

  protected function casts()
  {
        return [
            'raw_payload' => 'array'
        ];
  }

}
