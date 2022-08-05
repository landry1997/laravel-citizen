<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Mailes;
use App\Events\MyEvent;
use App\Jobs\FermetureJobs;
use App\Jobs\MakeQuickAlert;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Jobs\MakeSimpleAlert;
use App\Jobs\MakeNormalTracking;
use App\Http\Controllers\Controller;
use App\Models\QuickAlerte as Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\SimpleAlerte as Models;
use App\Models\DemandeSuivi as Modelss;
use App\Models\SuiviPosition as Model2;
use App\Notifications\SendPushNotification;

class AlerteController extends Controller
{
    private $serverKey;
    public function __construct()
    {
        $this->serverKey = config('app.firebase_server_key');
    }
    public function makeAlerteSuivi(Request $r){
        $r->validate([
            'user_id' => 'required|int',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'nom_lieu' => 'required|string',
            'type_commentaire' => 'required|int',
            'type' => 'required|int'
        ]);
        $input = $r->all();
        $data = [];
        if($r->type_commentaire == 1){
            $input['audio'] = \Storage::disk('public')->putFile('audio-alertes', $r->file('audio'));
        }
        if(!empty($r->media)){
            $input['media'] = \Storage::disk('public')->putFile('media-alertes', $r->file('media'));
        }
        $input['statut'] = "0";
        if(!empty($r->created_at)){
            $input['created_at'] = "$r->created_at";
        }
        $utilisateur = User::find($r->user_id);

        try {
            if($r->type == "0"){
                $code = "QA-".date('mdyis');
                $input['code'] = $code;
                $alerte = Model::create($input);
                $notification = Notification::create([
                    'code' => $code,
                    'contenu' =>"Nouvelle alerte rapide",
                    'contenu_en' =>"New quick alert",
                    'type' =>0,
                    'statut' =>0,
                ]);
                $title_fr = "Création d'alerte";
                $title_en = "Alert creation";
                $message_fr = "Nouvelle alerte rapide";
                $message_en = "New quick alert";
                $massa = Mailes::find(12);
                $type = "alert-channel";
                $this->dispatch(new MakeQuickAlert($r->user_id));
                broadcast(new MyEvent($r->user_id, array("type" => "info", "message" => "New quick alert!", "data" => $alerte)))->toOthers();
                $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $alerte, $type);
                $message2_fr = "Vous venez de faire une nouvelle alerte rapide";
                $message2_en = "New quick alert";
                $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $r->user_id);
            }elseif($r->type == "1"){
                $code = "SA-".date('mdyis');
                $input['code'] = $code;
                $alerte = Models::create($input);
                $notification = Notification::create([
                    'code' => $code,
                    'contenu' =>"Nouveau rapport",
                    'contenu_en' =>"New report",
                    'type' =>1,
                    'statut' =>0,
                ]);
                $title_fr = "Création de rapport";
                $title_en = "New report";
                $message_fr = "Nouveau rapport créé";
                $message_en = "New report";
                $this->dispatch(new MakeSimpleAlert($r->user_id));
                broadcast(new MyEvent($r->user_id, array("type" => "info", "message" => "New report!", "data" => $alerte)))->toOthers();

                $type = "alert-channel";
                $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $alerte, $type);
                $message2_fr = "Vous venez de faire un nouveau rapport";
                $message2_en = "You just made a new report";
                // return $alerte;
                $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $r->user_id);
                $massa = Mailes::find(13);
            }else{
                $code = "SUIVI-".date('mdyis');
                $input['code'] = $code;
                $alerte = Modelss::create($input);
                $alerte["position"] = [];
                $notification = Notification::create([
                    'code' => $code,
                    'contenu' =>"Nouvelle demande de suivi",
                    'contenu_en' =>"New New tracking request created",
                    'type' =>2,
                    'statut' =>0,
                ]);
                $title_fr = "Création d'alerte";
                $title_en = "New normal tracking";
                $message_fr = "Nouvelle demande de suivi";
                $message_en = "New normal tracking";
                $this->dispatch(new MakeNormalTracking($r->user_id));
                broadcast(new MyEvent($r->user_id, array("type" => "info", "message" => "New normal tracking!", "data" => $alerte)))->toOthers();

                $type = "alert-channel";
                $mama = $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $alerte, $type);
                $message2_fr = "Vous venez de faire une nouvelle demande de suivi";
                $message2_en = "New normal tracking";
                $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $r->user_id);
                $massa = Mailes::find(14);

            }
            $response = [
                'message_fr'    => "Opération réussie!",
                'message_en'    => "Successful operation    !",
                'data' => $alerte
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
        }
    }
    public function alerteByUser($user_id, $type ){
        // return $type;
        if($type == 0){
            $alerte = Model::with('user')->where('user_id', $user_id)->get();
        }elseif($type == 1){
            $alerte = Models::with('user')->where('user_id', $user_id)->get();
        }else{
            $alerte = Modelss::with(['user', 'position'])->where('user_id', $user_id)->get();
        }
        $response = [
            'message_fr'    => "Liste des alertes de cet utilisateur!",
            'message_en'    => "List of alerts for this user!",
            'information'    => "1== statut fermé et 0== statut ouvert",
            'data' => $alerte
        ];
        return response()->json($response);
    }
    public function detailAlert($code, $type){
        if($type == 0){
            $detail = Model::with('user')->where('code', $code)->first();
            $positions = [];
        }elseif($type == 1){
            $detail = Models::with('user')->where('code', $code)->first();
            $positions = [];
        }else{
            $detail = Modelss::with('user')->where('code', $code)->first();
            $positions = Model2::where('demande_suivi_id', $detail->code)->get();
            $response = [
                'message_fr'    => "Liste des alertes de cet utilisateur!",
                'message_en'    => "List of alerts for this user!",
                'data' => [
                    "demande_suivi" => $detail,
                    "positions" => $positions,
                    ]
            ];
            return response()->json($response);
        }
        $response = [
            'message_fr'    => "Liste des alertes de cet utilisateur!",
            'message_en'    => "List of alerts for this user!",
            'data' => $detail
        ];
        return response()->json($response);
    }
    public function fermetureAlertSuivi(Request $r){
        $r->validate([
            'type' => 'required|int',
            'fermeur'=> 'required',
            'code'=> 'required',
            'type_motif_fermeture'=> 'required',
        ]);
        $fermeurs = User::where('id',$r->fermeur)->first();
        if($fermeurs && $fermeurs->role_id != 2 && $fermeurs->statut == 1){
                if($r->type == "0"){
                    try {
                        $fermeture = Model::where('code',$r->code)->first();
                        if($r->type_motif_fermeture == 0){
                            if(empty($fermeture->commentaire_admin)){
                                $fermeture->commentaire_admin = $r->commentaire_admin;
                            }elseif(!empty($fermeture->commentaire_admin)){
                                $response = [
                                    'message_fr'    => "Vous essayez de fermer une alerte déjà fermée",
                                    'message_en'    => "You are trying to close an already closed alert",
                                ];
                                return response()->json($response, 422);
                            }else{
                                $response = [
                                    'message_fr'    => "Vous devez entrer un texte valide comme motif de fermeture",
                                    'message_en'    => "You must enter a valid text as the closing reason",
                                ];
                                return response()->json($response, 422);
                            }
                        }else{
                            if(empty($fermeture->commentaire_admin_audio)){
                                $fermeture->commentaire_admin_audio = \Storage::disk('public')->putFile('motifs-fermeture-audio', $r->file('commentaire_admin_audio'));
                            }else{
                                $response = [
                                    'message_fr'    => "Vous devez entrer un audio valide comme motif de fermeture",
                                    'message_en'    => "You must enter a valid song as the closing reason",
                                ];
                                return response()->json($response, 422);
                            }
                        }
                        $fermeture->statut = 1;
                        $fermeture->fermeur = $fermeurs->id;
                        $fermeture->save();
                        $this->dispatch(new FermetureJobs($fermeture->user_id));
                        broadcast(new MyEvent($r->fermeur, array("type" => "info", "message" => "Une alerte rapide vient d'être fermée!", "data" => $fermeture)))->toOthers();
                        $title_fr = "Fermeture d'alerte";
                        $title_en = "Quick alert closed";
                        $message_fr = "Une alerte rapide vient d'être fermée";
                        $message_en = "New quick alert";
                        $type = "close-alert-channel";
                        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $fermeture, $type);
                        $response = [
                            'message_fr'    => "Alerte rapide fermée avec succès!",
                            'message_en'    => "Successfully closed early warning!",
                            'data' => $fermeture
                        ];
                        return response()->json($response, 200);
                    } catch (\Throwable $th) {
                        $response = [
                            'message_fr'    => "Saisissez un code valide",
                            'message_en'    => "Enter a valid code",
                        ];
                        return response()->json($response, 422);
                    }
                }elseif($r->type == "1"){
                    try {
                        $fermeture = Models::where('code',$r->code)->first();
                        if($r->type_motif_fermeture == 0){
                            if(empty($fermeture->commentaire_admin)){
                                $fermeture->commentaire_admin = $r->commentaire_admin;
                            }elseif(!empty($fermeture->commentaire_admin)){
                                $response = [
                                    'message_fr'    => "Vous essayez de fermer une alerte déjà fermée",
                                    'message_en'    => "You are trying to close an already closed alert",
                                ];
                                return response()->json($response, 422);
                            }else{
                                $response = [
                                    'message_fr'    => "Vous devez entrer un texte valide comme motif de fermeture",
                                    'message_en'    => "You must enter a valid text as the closing reason",
                                ];
                                return response()->json($response, 422);
                            }
                        }else{
                            if(empty($fermeture->commentaire_admin_audio)){
                                $fermeture->commentaire_admin_audio = \Storage::disk('public')->putFile('motifs-fermeture-audio', $r->file('commentaire_admin_audio'));
                            }else{
                                $response = [
                                    'message_fr'    => "Vous devez entrer un audio valide comme motif de fermeture",
                                    'message_en'    => "You must enter a valid song as the closing reason",
                                ];
                                return response()->json($response, 422);
                            }
                        }
                        $fermeture->statut = 1;
                        $fermeture->commentaire_admin = $r->commentaire_admin;
                        $fermeture->fermeur = $fermeurs->id;
                        $fermeture->save();
                        $this->dispatch(new FermetureJobs($fermeture->user_id));
                        broadcast(new MyEvent($r->fermeur, array("type" => "info", "message" => "Un rapport vient d'être fermé!", "data" => $fermeture)))->toOthers();
                        $title_fr = "Fermeture de rapport";
                        $title_en = "Report closed";
                        $message_fr =  "Un rapport vient d'être fermé";
                        $message_en =  "A report has just been closed";
                        $type = "close-alert-channel";
                        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $fermeture, $type);
                        $response = [
                            'message_fr'    => "Rapport fermé avec succès!",
                            'message_en'    => "Report closed successfully!",
                            'data' => $fermeture
                        ];
                        return response()->json($response, 200);
                    } catch (\Throwable $th) {
                        $response = [
                            'message_fr'    => "Saisissez un code valide",
                            'message_en'    => "Enter a valid code",
                        ];
                        return response()->json($response, 422);
                    }
                }else{
                    try {
                        $fermeture = Modelss::where('code',$r->code)->first();
                        if($r->type_motif_fermeture == 0){
                            if(empty($fermeture->commentaire_admin)){
                                $fermeture->commentaire_admin = $r->commentaire_admin;
                            }elseif(!empty($fermeture->commentaire_admin)){
                                $response = [
                                    'message_fr'    => "Vous essayez de fermer une alerte déjà fermée",
                                    'message_en'    => "You are trying to close an already closed alert",
                                ];
                                return response()->json($response, 422);
                            }else{
                                $response = [
                                    'message_fr'    => "Vous devez entrer un texte valide comme motif de fermeture",
                                    'message_en'    => "You must enter a valid text as the closing reason",
                                ];
                                return response()->json($response, 422);
                            }
                        }else{
                            if(empty($fermeture->commentaire_admin_audio)){
                                $fermeture->commentaire_admin_audio = \Storage::disk('public')->putFile('motifs-fermeture-audio', $r->file('commentaire_admin_audio'));
                            }else{
                                $response = [
                                    'message_fr'    => "Vous devez entrer un audio valide comme motif de fermeture",
                                    'message_en'    => "You must enter a valid song as the closing reason",
                                ];
                                return response()->json($response, 422);
                            }
                        }
                        $fermeture->statut = 1;
                        $fermeture->commentaire_admin = $r->commentaire_admin;
                        $fermeture->fermeur = $fermeurs->id;
                        $fermeture->save();
                        $this->dispatch(new FermetureJobs($fermeture->user_id));
                        broadcast(new MyEvent($r->fermeur, array("type" => "info", "message" => "Une demande de suivi vient d'être fermée!", "data" => $fermeture)))->toOthers();
                        $title_fr = "Fermeture de demande de suivi";
                        $title_en = "Normal tracking closed";
                        $message_fr = "Une demande de suivi vient d'être fermée";
                        $message_en = "Normal tracking closed";
                        $type = "close-alert-channel";
                        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $fermeture, $type);
                        $response = [
                            'message_fr'    => "Demande de suivi fermée avec succès!",
                            'message_en'    => "Follow-up request successfully closed!",
                            'data' => $fermeture
                        ];
                        return response()->json($response, 200);
                    } catch (\Throwable $th) {
                        $response = [
                            'message_fr'    => "Saisissez un code valide",
                            'message_en'    => "Enter a valid code",
                        ];
                        return response()->json($response, 422);
                    }
                }
        }else{
            $response = [
                'message_fr'    => "L'utilisateur qui veut fermer cette alerte n'est pas autorisé",
                'message_en'    => "The user who wants to close this alert is not allowed",
            ];
            return response()->json($response, 422);
        }
    }
    public function allAlerteByUser($user_id){
        $quick = Model::with('user')->where('user_id', $user_id)->orderByDesc('id')->get();
        $simple = Models::with('user')->where('user_id', $user_id)->orderByDesc('id')->get();
        $suivi = Modelss::with(['user', 'position'])->where('user_id', $user_id)->orderByDesc('id')->get();

        $quickSimple = $quick->mergeRecursive($simple);
        $quickSimpleSuivi = $quickSimple->mergeRecursive($suivi);
        $result = $quickSimpleSuivi->all();
        $count = count($result);

        $count = count($result);

        $response = [
            'message_fr'    => "Liste des alertes de cet utilisateur!",
            'message_en'    => "List of alerts for this user!",
            'compte'    => $count,
            'Alert' => $result,
        ];
        return response()->json($response);
    }

    public function allAlerte(){
        $quick = Model::with('user')->orderByDesc('id')->get();
        $simple = Models::with('user')->orderByDesc('id')->get();
        $suivi = Modelss::with(['user', 'position'])->orderByDesc('id')->get();

        $quickSimple = $quick->mergeRecursive($simple);
        $quickSimpleSuivi = $quickSimple->mergeRecursive($suivi);
        $result = $quickSimpleSuivi->all();
        $count = count($result);

        $count = count($result);

        $response = [
            'message_fr'    => "Liste des alertes de cet utilisateur!",
            'message_en'    => "List of alerts for this user!",
            'compte'    => $count,
            'Alert' => $result,
        ];
        return response()->json($response);
    }
    public function sendPush ($message_fr,$message_en, $title_fr,$title_en, $data, $type)
    {
        try{
                $data = [
                    "to" => "/topics/".$type,
                    "notification" =>[
                            "title" => $title_fr,
                            "title_en" => $title_en,
                            "body" => $message_fr,
                            "body_en" => $message_en,
                            "icon" => url('/storage/settings/May2022/8bEb9qzJIvBqOiEjd89r.png')
                        ],
                        "data" =>$data
                ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=AAAAdkXaLoY:APA91bGTdEiamlEzgeEXawVftAkAfoDb8gjdvE3WWJ4OOcSWak1agEccmpfkw3qVBGrs3KBGmLESwA9EalpTd9KfmT3enttQAiwUa4LJ1kshJoKxZt2ypoDvVtoDteL660F4_b4fmyUV',
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_exec($ch);
        } catch (\Throwable $th) {
        }
    }
    public function UserPush ($message_fr,$message_en, $title_fr,$title_en, $user)
    {
        try{
            $recever = User::find($user);
            $data = [
                "to" => $recever->device_token,
                "notification" =>
                    [
                        "title" => $title_en,
                        "body" => $message_en,
                        "icon" => url('/storage/settings/May2022/8bEb9qzJIvBqOiEjd89r.png')
                    ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=AAAAdkXaLoY:APA91bGTdEiamlEzgeEXawVftAkAfoDb8gjdvE3WWJ4OOcSWak1agEccmpfkw3qVBGrs3KBGmLESwA9EalpTd9KfmT3enttQAiwUa4LJ1kshJoKxZt2ypoDvVtoDteL660F4_b4fmyUV',
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_exec($ch);
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function updateDeviceToken(Request $r)
    {
        $r->validate([
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        $user = User::find($r->user_id);
        if ($user) {
            $user['device_token'] = $r->device_token;
            $user->save();
            $response = [
                'message_fr'    => "Device token enregistré avec succès!",
                'message_en'    => "Device token saved successfully",
                'user' => $user
            ];
            return response()->json($response, 200);
        }else {

            $response = [
                'message_fr'    => "l'utilisateur n'existe pas",
                'message_en'    => "An error occured, please try again later"
            ];
            return response()->json($response, 422);
        }
    }
}
