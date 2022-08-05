<?php

namespace App\Observers;
use Carbon\Carbon;
use App\Models\SimpleAlerte as Model;
use App\Models\CreationUserHistorique as Models;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SimpleAlertObserver
{
    public function created(Model $simple)
    {
        $code = "SA-".date('mdyis');
        $simple->code = $code;
    }
}
