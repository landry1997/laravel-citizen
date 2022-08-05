<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;

class TermsCondition extends Model
{
    use  HasFactory;
    protected $table = "terms_conditions";
    protected $fillable = [
        'content',
    ];
}
