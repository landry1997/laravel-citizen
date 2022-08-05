<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Desinstallation as Modelss;
use App\Http\Controllers\Controller;
use App\Models\CreationUserHistorique as Model;
use App\Models\ValidateUserHistorique as Models;

class HystoryController extends Controller
{
    public function creation_historique(){
        $historique = Model::all();
        $response = [
            'message_fr'    => "Historique de creation!",
            'message_en'    => "creation history!",
            'data' => $historique
        ];
        return response()->json($response);
    }
    public function validation_historique(){
        $historique = Models::all();
        $response = [
            'message_fr'    => "Historique des validation users!",
            'message_en'    => "validation user history!",
            'data' => $historique
        ];
        return response()->json($response);
    }
    public function desinstallation_historique(){
        $historique = Modelss::all();
        $response = [
            'message_fr'    => "Historique des desinstallations!",
            'message_en'    => "desinstallation user history!",
            'data' => $historique
        ];
        return response()->json($response);
    }
}
