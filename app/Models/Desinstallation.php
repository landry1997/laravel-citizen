<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use TCG\Voyager\Traits\Translatable;

class Desinstallation extends Model
{
    use HasFactory;
    // use Translatable;
    protected $table = "desinstallation";
    // protected $translatable = ['title', 'body'];
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'lieu',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
