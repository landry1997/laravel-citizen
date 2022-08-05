<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;

class DemandeSuivi extends Model
{
    use  HasFactory;
    // use Translatable;
    // use SoftDeletes;
    // protected $dates = ['deleted_at'];
    protected $table = "demande_suivi";
    //  protected $translatable = ['title', 'body'];
    protected $fillable = [
        'latitude',
        'longitude',
        'user_id',
        'created_at',
        'nom_lieu',
        'commentaire',
        'commentaire_admin',
        'statut',
        'code',
        'audio',
        'type_commentaire',
        'type_motif_fermeture',
        'commentaire_admin_audio',
        'fermeur',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function position () {
        return $this->hasMany(SuiviPosition::class);
    }
}
