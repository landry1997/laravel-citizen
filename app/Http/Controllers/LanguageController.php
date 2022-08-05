<?php

namespace App\Http\Controllers;

use App\Models\User as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function index($locales){
        App::setlocale(Session::get('locale'));
        Session::put('locale', $locales);
        Model::find(Auth::user()->id)->update(['settings' => json_encode(["locale"=>$locales])]);
        // dd(Auth::user()->id, $locales, Session::get('locale'), json_encode(["locale"=>$locales]));
        return redirect()->back();
    }
}
