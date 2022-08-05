<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadNotification extends Model
{
    use HasFactory;
    // protected $translatable = ['title', 'body'];
    protected $table = "read_notification";
    protected $fillable = [
        'user_id',
        'notification_id',
    ];
}
