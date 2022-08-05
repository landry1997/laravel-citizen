<?php

namespace App\Http\Controllers\Web;

use App\Models\QuickAlerte as Model1;
use App\Models\DemandeSuivi as Model2;
use App\Models\SimpleAlerte as Model3;
use Illuminate\Http\Request;
use App\Models\ReadNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification as Model;

class NotificationReadController extends Controller
{
    public function readNotification(Request $r, $id)
    {
        $notification = ReadNotification::create([
            'user_id' => Auth::user()->id,
            'notification_id' => $id,
        ]);
        $read = Model::where('id',$id)->first();
        $read->statut = 1;
        $read->save();
        if ($r->type == 0) {
            $rapide = Model1::where('code', $r->code)->first();
            return redirect('admin/quick-alerte/'.$rapide->id)->with([
                'message'    => "Opération éffectuée avec succès!",
                'alert-type' => 'success',
            ]);
        } elseif ($r->type == 1) {
            $rapide = Model3::where('code', $r->code)->first();
            return redirect('admin/simple-alerte/'.$rapide->id)->with([
                'message'    => "Opération éffectuée avec succès!",
                'alert-type' => 'success',
            ]);
        }
        elseif ($r->type == 2) {
            $rapide = Model2::where('code', $r->code)->first();
            return redirect('admin/demande-suivi/'.$rapide->id)->with([
                'message'    => "Opération éffectuée avec succès!",
                'alert-type' => 'success',
            ]);
        }


    }
}
