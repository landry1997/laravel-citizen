<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Notification extends Model
{
    use HasFactory;
    // use Translatable;
    // protected $translatable = ['title', 'body'];
    protected $table = "notification";
    protected $fillable = [
        'code',
        'contenu',
        'contenu_en',
        'donnee',
        'type',
        'statut',
        'notif_to',
        'lien'
    ];
}
