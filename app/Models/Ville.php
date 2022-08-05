<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;

class Ville extends Model
{
    use HasFactory;
    use SoftDeletes;

    // use Translatable;
    protected $dates = ['deleted_at'];
    // protected $translatable = ['title', 'body'];
    protected $table = "ville";
    protected $fillable = [
        'region_id',
        'nom',
    ];
}
