<?php

namespace App\Observers;
use Carbon\Carbon;
use App\Models\User as Model;
use App\Models\CreationUserHistorique as Models;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(Model $user)
    {
        // $user = Models::create([
        //     'user_id' => $user->id,
        // ]);
    }
}
