<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    public function scopeDeveloper($query)
    {
        if(!in_array(Auth::user()->role_id, [2,3,4,5,6])){
            return $query->where('id', '<>', 1);
        }
    }
}
