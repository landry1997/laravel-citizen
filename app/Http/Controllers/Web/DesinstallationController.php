<?php

namespace App\Http\Controllers\Web;

use App\Events\MyEvent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Desinstallation as Model;

class DesinstallationController extends Controller
{
    public function manage_desinstallation(Request $r, $code){
        $r->validate([
            'type' => 'required'
        ]);
        if($r->type == 1){
            $validate = Model::where('code',$code)->first();
            $validate->statut = 1;
            $validate->save();
            broadcast(new MyEvent(Auth::user()->id, array("type" => "info", "message" => "Une demande de désinstallation vient d'être validée!", "data" => $validate->code)))->toOthers();
        }else{
            $validate = Model::where('code',$code)->first();
            $validate->statut = 2;
            $validate->save();broadcast(new MyEvent(Auth::user()->id, array("type" => "info", "message" => "Une demande de désinstallation vient d'être refusée!", "data" => $validate->code)))->toOthers();
        }
        return redirect('admin/desinstallation')->with([
            'message'    => "Opération éffectuée avec succès!",
            'alert-type' => 'success',
        ]);
    }
}
