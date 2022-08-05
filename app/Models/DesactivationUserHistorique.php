<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DesactivationUserHistorique extends Model
{
    use HasFactory;

    protected $table = "historique_desactivation";
    protected $fillable = [
        'validator',
        'user_code',
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
