<?php

namespace App\Http\Controllers\Api;

use App\Models\Ville as Model;
use Illuminate\Http\Request;
use App\Events\MyEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class VilleController extends Controller
{
    public function all_ville(){
        $ville = Model::where('deleted_at', null)->select('id', 'region_id', 'nom')->get();

        $response = [
            'message_fr' => "Liste des villes",
            'message_en' => 'Cyties list',
            "data"=> $ville,
        ];
        return response()->json($response, 200);
    }
}
