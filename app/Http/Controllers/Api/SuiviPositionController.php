<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\DemandeSuivi as Model;
use App\Models\SuiviPosition as Models;

class SuiviPositionController extends Controller
{
    public function lastPosition($user_id){
        $position = Models::where('user_id', $user_id)->OrderByDesc('id')->first();
        try {
            if($position){
                $demande = Model::where('code', $position->demande_suivi_id)->first();
                $response = [
                    'message_fr'    => "Derniere position!",
                    'message_en'    => "Last Position!",
                    'data' => [
                        "demande_suivi" => $demande,
                        "positions" => $position,
                        ]
                ];
                return response()->json($response, 200);
            }else{
                $response = [
                    'message_fr'    => "Derniere position!",
                    'message_en'    => "Last Position!",
                    'data' => [
                        "demande_suivi" => "",
                        "positions" => "",
                        ]
                ];
                return response()->json($response, 200);
            }
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
    }
    public function allPosition($suivi_id){
        $detail = Model::where('code', $suivi_id)->first();
        try {
            if($detail){
                $positions = Models::where('demande_suivi_id', $detail->code)->get();
                $response = [
                    'message_fr'    => "position!",
                    'message_en'    => "Position!",
                    'data' => [
                        "demande_suivi" => $detail,
                        "positions" => $positions,
                        ]
                ];
                return response()->json($response);
            }else{
                $response = [
                    'message_fr'    => "position!",
                    'message_en'    => "Position!",
                    'data' => [
                        "demande_suivi" => "",
                        "positions" => "",
                        ]
                ];
                return response()->json($response);
            }
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
    }
    public function suiviPosition(Request $r){
        $r->validate([
            'user_id' => 'required|int',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'nom_lieu' => 'required|string',
            'demande_suivi_id' => 'required',
        ]);
        $demande = Model::where('code', $r->demande_suivi_id)->first();
        if($demande){
            if($r->user_id == $demande->user_id){
                try {
                    $input = $r->all();
                    if(!empty($r->created_at)){
                        $input['created_at'] = "$r->created_at";
                    }
                    $suiviPosition = Models::create($input);
                    $response = [
                        'message_fr'    => "Position enregistrée avec succès!",
                        'message_en'    => "Successfully registered position!",
                        'data' => $suiviPosition
                    ];
                    return response()->json($response, 200);
                } catch (\Throwable $th) {
                    return $th;
                }
            }else{

                $response = [
                    'message_fr'    => "Vous n'êtes pas autorisé à partager votre position pour cette demande de suivi",
                    'message_en'    => "You are not allowed to share your position for this follow-up request",
                ];
                return response()->json($response, 422);
            }
        }else{
            $response = [
                'message_fr'    => "Désolé mais le code de la demande de suivi est incorrect!",
                'message_en'    => "Sorry but the code of the follow-up request is incorrect!",
                'data' => []
            ];
            return response()->json($response);
        }
    }
}
