<?php

namespace App\Models;

use App\Models\OauthAccessToken;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CreationUserHistorique as Models;

use App\Models\ValidateUserHistorique as Model2;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    // use Translatable;
    protected $dates = ['deleted_at'];
    // protected $translatable = ['title', 'body'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'settings',
        'role_id',
        'phone',
        'fcm_token',
        'ville',
        'region',
        'email',
        'password',
        'avatar',
        'code',
        'statut',
        'remember_token',
        'device_key',
        'code_reset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'settings' => 'json'
    ];
        public function scopeDeveloper($query)
        {
            if(!in_array(Auth::user()->role_id, [2,3,4,5,6])){
                return $query->where('role_id', '<>', 1);
            }
        }

    public function villes()
    {
        return $this->belongsTo(Ville::class, 'ville');
    }
    public function setSettingsAttribute($value) { $this->attributes['settings'] = $value; }
    public function regions()
    {
        return $this->belongsTo(Region::class, 'region');
    }
    public function simple()
    {
        return $this->belongsTo(SimpleAlerte::class);
    }
}
