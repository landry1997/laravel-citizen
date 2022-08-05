<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region as Model;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function all_region(){
        $ville = Model::where('deleted_at', null)->select('id', 'nom')->get();
        $response = [
            'message_fr' => "Liste des Regions",
            'message_en' => 'Regions list',
            "data"=> $ville,
        ];
        return response()->json($response);
    }
}
