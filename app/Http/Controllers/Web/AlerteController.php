<?php

namespace App\Http\Controllers\Web;

use PDF;
use App\Models\User;
use App\Events\MyEvent;
use App\Jobs\FermetureJobs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\QuickAlerte as Model;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SimpleAlerte as Models;
use App\Models\DemandeSuivi as Modelss;
use App\Exports\QuickAlerteExport as Model2;
use App\Exports\DemandeSuiviExport as Model1;
use App\Exports\SimpleAlerteExport as Model3;

class AlerteController extends Controller
{
    public function __construct()
    {
        $this->serverKey = config('app.firebase_server_key');
    }
    public function fermetureAlertSuivi(Request $r){
        $r->validate([
            'type' => 'required|int',
            'commentaire_admin' => 'required',
        ]);
        if($r->type == "0"){
            $fermeture = Model::where('code',$r->code)->first();
            if($fermeture->statut == 1){
                $fermeture->commentaire_admin = $r->commentaire_admin;
                $fermeture->type_motif_fermeture = 0;
                $fermeture->save();
                $this->dispatch(new FermetureJobs($fermeture->user_id));
                return redirect('admin/quick-alerte')->with([
                    'message'    => " Quick alerte modifiée avec succès!",
                    'alert-type' => 'success',
                ]);
            }else{
                $fermeture->statut = 1;
                $fermeture->commentaire_admin = $r->commentaire_admin;
                $fermeture->type_motif_fermeture = 0;
                $fermeture->save();
                $this->dispatch(new FermetureJobs($fermeture->user_id));
                broadcast(new MyEvent(\Auth::user()->id, array("type" => "info", "message" => "Une alerte rapide vient d'être fermée!", "data" => $fermeture->code)))->toOthers();

                $title_fr = "Fermeture d'alerte";
                $title_en = "Closed alert";
                $message_fr = "Une alerte rapide vient d'être fermée";
                $message_en = "An early warning has just been closed";
                $type = "close-alert-channel";
                $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $fermeture, $type);
                return redirect('admin/quick-alerte')->with([
                    'message'    => " Quick alerte fermée avec succès!",
                    'alert-type' => 'success',
                ]);
            }
        }elseif($r->type == "1"){
            $fermeture = Models::where('code',$r->code)->first();
            if($fermeture->statut == 1){
                $fermeture->commentaire_admin = $r->commentaire_admin;
                $fermeture->type_motif_fermeture = 0;
                $fermeture->save();
                $this->dispatch(new FermetureJobs($fermeture->user_id));
                return redirect('admin/simple-alerte')->with([
                    'message'    => "Alerte simple modifiée avec succès!",
                    'alert-type' => 'success',
                ]);
            }else{
                $fermeture->statut = 1;
                $fermeture->commentaire_admin = $r->commentaire_admin;
                $fermeture->type_motif_fermeture = 0;
                $fermeture->save();
                $title_fr = "Fermeture de rapport";
                $title_en = "Report closed";
                $message_fr =  "Un rapport vient d'être fermé";
                $message_en =  "A report has just been closed";
                $type = "close-alert-channel";
                $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $fermeture, $type);
                return redirect('admin/simple-alerte')->with([
                    'message'    => "Alerte simple fermée avec succès!",
                    'alert-type' => 'success',
                ]);
            }
        }else{
            $fermeture = Modelss::where('code',$r->code)->first();
            if($fermeture->statut == 1){
                $fermeture->commentaire_admin = $r->commentaire_admin;
                $fermeture->type_motif_fermeture = 0;
                $fermeture->save();
                return redirect('admin/demande-suivi')->with([
                    'message'    => "Demande de suivi modifiée avec succès!",
                    'alert-type' => 'success',
                ]);
            }else{
                $fermeture->statut = 1;
                $fermeture->commentaire_admin = $r->commentaire_admin;
                $fermeture->type_motif_fermeture = 0;
                $fermeture->save();
                $title_fr = "Fermeture de demande de suivi";
                $title_en = "Normal tracking closed";
                $message_fr = "Une demande de suivi vient d'être fermée";
                $message_en = "Normal tracking closed";
                $type = "close-alert-channel";
                $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $fermeture, $type);
                $receiver = \Auth::user()->id;
                return redirect('admin/demande-suivi')->with([
                    'message'    => "Demande de suivi fermée avec succès!",
                    'alert-type' => 'success',
                ]);
            }
        }
    }
    public function allDemandeSuivi()
    {
        return Excel::download(new Model1, 'all follow-up request.xlsx');
    }
    public function allDemandeSuivi2()
    {
        return Excel::download(new Model1, 'all follow-up request.pdf');
    }
    public function allQuickAlerte()
    {
        return Excel::download(new Model2, 'all quick alert.xlsx');
    }
    public function allQuickAlerte2()
    {
        return Excel::download(new Model2, 'all quick alert.pdf');
    }
    public function allSimpleAlerte()
    {
        return Excel::download(new Model3, 'all simple alert.xlsx');
    }
    public function allSimpleAlerte2()
    {
        return Excel::download(new Model3, 'all simple alert.pdf');
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
}
