<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;

class SimpleAlerte extends Model
{
    use  HasFactory;
    // use Translatable;
    // use SoftDeletes;
    // protected $dates = ['deleted_at'];
    protected $table = "simple_alerte";
    // protected $translatable = ['title', 'body'];
    protected $fillable = [
        'latitude',
        'longitude',
        'user_id',
        'nom_lieu',
        'created_at',
        'commentaire',
        'commentaire_admin',
        'statut',
        'code',
        'media',
        'fermeur',
        'type_motif_fermeture',
        'commentaire_admin_audio',
        'audio',
        'type_media',
        'type_commentaire',
    ];
    // public function save(array $options = [])
    // {
    //     $code = "SA-".date('mdyis');
    //     $this->code = $code;
    //     return parent::save();
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
