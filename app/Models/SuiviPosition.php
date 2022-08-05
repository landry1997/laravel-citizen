<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use TCG\Voyager\Traits\Translatable;

class SuiviPosition extends Model
{
    use HasFactory;
    // use Translatable;

    protected $table = "suivi_position";
    // protected $translatable = ['title', 'body'];
    protected $fillable = [
        'latitude',
        'longitude',
        'created_at',
        'user_id',
        'nom_lieu',
        'demande_suivi_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function demande()
    {
        return $this->belongsTo(DemandeSuivi::class, 'demande_suivi_id');
    }
}
