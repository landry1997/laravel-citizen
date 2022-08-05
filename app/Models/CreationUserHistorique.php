<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use TCG\Voyager\Traits\Translatable

class CreationUserHistorique extends Model
{
    use  HasFactory;
    protected $table = "creation_user_historique";
    protected $fillable = [
        'user_code',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_code');
    }
}
