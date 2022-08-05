<?php

namespace App\Http\Controllers\Api;

use App\Events\MyEvent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Desinstallation as Model;

class DesinstallationController extends Controller
{
    //
    public function request_desinstallation(Request $r){
        $r->validate([
            'user_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'lieu' => 'required',
        ]);
        try {
            $desinstallation = Model::create([
                'user_id' => $r->user_id,
                'latitude' => $r->latitude,
                'lieu' => $r->lieu,
                'longitude' => $r->longitude
            ]);
            broadcast(new MyEvent(\Auth::user()->id, array("type" => "info", "message" => "Nouvelle désinstallation initiée!", "data" => $desinstallation)))->toOthers();
            $response = [
                'message_fr'    => "Désinstallation initiée!",
                'message_en'    => "Uninstallation initiated!",
                'data' => $desinstallation
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "Une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
    }
}
