<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Mailes extends Model
{
    use HasFactory;

    // use Translatable;
    // protected $translatable = ['title', 'body'];
    protected $table = "mailes";
    protected $fillable = [
        'type',
        'header',
        'contenu',
        'subject'
    ];
}
