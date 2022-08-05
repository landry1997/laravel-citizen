<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use TCG\Voyager\Traits\Translatable;

class ValidateUserHistorique extends Model
{
    use HasFactory;
    // use Translatable;
    protected $table = "validate_user_historique";
    // protected $translatable = ['title', 'body'];
    protected $fillable = [
        'user_code',
        'validator',
    ];
    public function save(array $options = [])
    {
        if (Auth::user()) {
            $this->validator = Auth::user()->id;
        }
        return parent::save();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function valideur()
    {
        return $this->belongsTo(User::class, 'validator');
    }
}
