<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Exports\PositionsExport as Export;
use App\Models\DemandeSuivi as Models;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SuiviPositionController extends Controller
{
    public function suivi_position(Request $r)
    {
        $suivi_id = $r->suivi_id;
        return Excel::download(new Export($suivi_id), 'suivi positions.xlsx');
    }
    public function suivi_position2(Request $r)
    {
        $suivi_id = $r->suivi_id;
        return Excel::download(new Export($suivi_id), 'suivi positions.pdf');
    }

    public function suivi_position_user(Request $r)
    {
        $user_id = $r->user_id;
        $detail = Models::where('user_id', $user_id)->OrderByDesc('id')->first();
        return Excel::download(new Export($detail->code), 'suivi positions.xlsx');
    }
    public function suivi_position_user2(Request $r)
    {
        $user_id = $r->user_id;
        $detail = Models::where('user_id', $user_id)->OrderByDesc('id')->first();
        return Excel::download(new Export($detail->code), 'suivi positions.pdf');
    }
}
