<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Region extends Model
{
    use HasFactory;
    use SoftDeletes;
    // use Translatable;
    protected $dates = ['deleted_at'];
    protected $table = "region";
    // protected $translatable = ['title', 'body'];
    protected $fillable = [
        'nom',
        'statut',
    ];
}
