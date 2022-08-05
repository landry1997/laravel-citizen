<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\TermsCondition;

class ViewController extends Controller
{
    public function suivi_demand(){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }
        return view('voyager::demande-suivi.map');
    }
    public function quick_alert(){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }
        return view('voyager::quick-alerte.map');
    }
    public function simple_alert(){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }
        return view('voyager::simple-alerte.map');
    }
    public function all_notifications(Request $r){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }

        $notifications = Notification::paginate(5);
        return view('voyager::all-notification.read',compact('notifications'));
    }
    public function cso(){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }
        return view('voyager::users.cso');
    }
    public function csol(){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }
        return view('voyager::users.csol');
    }
    public function singlemap(){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }
        return view('voyager::desinstallation.singlemap');
    }
    public function terms(){
        \App::setlocale(\Session::get('locale'));
        \Session::put('locale', \Session::get('locale'));
        if (\Auth::user()) {
            User::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
        }
        $terms = TermsCondition::all();
        return view('voyager::terms', compact('terms'));
    }
}
