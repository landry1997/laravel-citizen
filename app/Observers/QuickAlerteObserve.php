<?php

namespace App\Observers;
use Carbon\Carbon;
use App\Models\QuickAlerte as Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class QuickAlerteObserve
{
    public function created(Model $user)
    {
        // $maaa = "USER-".date('dymis');
        // $code = Str::slug($this->name).$maaa;
        // $user->code = $code;
    }
}
