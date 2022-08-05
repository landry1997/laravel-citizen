<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;

class QuickAlerte extends Model
{
    use  HasFactory;
    // use Translatable;
    // use SoftDeletes;
    // protected $dates = ['deleted_at'];
    protected $table = "quick_alerte";
    // protected $translatable = ['title', 'body'];
    protected $fillable = [
        'latitude',
        'longitude',
        'created_at',
        'user_id',
        'nom_lieu',
        'commentaire',
        'commentaire_admin',
        'statut',
        'type_motif_fermeture',
        'commentaire_admin_audio',
        'code',
        'audio',
        'type_commentaire',
        'fermeur',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
