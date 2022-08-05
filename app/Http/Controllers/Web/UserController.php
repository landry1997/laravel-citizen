<?php

namespace App\Http\Controllers\Web;

use PDF;
use App\Models\User;
use App\Jobs\SendMail;
use App\Models\Mailes;
use App\Events\MyEvent;
use App\Jobs\ActiveUser;
use App\Jobs\PromoteUser;
use App\Jobs\DeactiveUser;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use App\Jobs\RetrogradeUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User as Auths;
use App\Models\User as Model;
use TCG\Voyager\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DesactivationUserHistorique;
use App\Notifications\SendPushNotification;
use App\Models\CreationUserHistorique as Models;
use App\Models\ValidateUserHistorique as Model2;

class UserController extends Controller
{
    public function __construct()
    {
        $this->serverKey = config('app.firebase_server_key');
    }
    public function forgotPassword(Request $r, Model $users)
    {
        $r->validate([
            'email' => 'required',
        ]);
        $user = Auths::where('email', $r->email)->first();
        if($user){
            $massa = Mailes::find(9);
            $details = [
                'subject' => $massa->sujet,
                'header' => $massa->header,
                'btn' => route('users.reset'),
                'body' => $massa->contenu
            ];
            try {
                Mail::to($r['email'])->send(new \App\Mail\Mailer($details));
            } catch (\Exception $e) {
                $redirect = redirect()->back();
                return $redirect->with('error', "Please enter a valid email address!");
            }
            return redirect('admin/login');
        }else{
            $redirect = redirect()->back();
            return $redirect->with('error', "Please enter a valid email address!");
        }
    }
    public function resetPwd(Request $r)
    {
        $r->validate([
            'email' => 'required',
            'password' => 'min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8',
        ]);
        $user = Auths::where('email', $r->email)->update(array('password' => HASH::make($r->password)));
            return redirect('admin/login');
    }

    public function resetPwdView()
    {
       return view('auth.passwords.reset');
    }
    public function finishCreateUser(Request $r){
        $r->validate([
            'name' => 'required',
            'password' => 'min:6',
            // 'password_confirmation' => 'required_with:password|same:password',
            'region' => 'required',
            'code' => 'required',
            'ville' => 'required',
        ]);
        $password = Hash::make($r->password);
        $input = $r->all();
        $input['password'] = Hash::make($r->password);
        $input['statut'] = 1;
        $user = Model::where('code', $r->code)->first();
        if($user != null){
            $user->fill($input)->save();
            try {
                $massa = Mailes::find(1);
                $details = [
                    'subject' => $massa->sujet,
                    'header' => $massa->header,
                    'btn' => 'no',
                    'body' => $massa->contenu
                ];
                Mail::to($user['email'])->send(new \App\Mail\Mailer($details));
                try {
                    $receiver = Auth::user()->id;
                    $message_fr = "Un utilisateur vient de terminer son inscription sur la plateforme";
                    $message_en = "A user has just finished his registration on the platform";
                    $title_fr = "Mise à jour des informations";
                    $title_en = "Update of information";
                    $type ="finish-register-channel";
                    $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
                 } catch (\Throwable $th) {
                }
                return redirect('admin/login')->with('success', "Cet utilisateur est maintenant activé!");
            } catch (\Throwable $th) {
                return redirect('admin/login')->with('success', "Cet utilisateur est maintenant activé!");
            }
        }else{
            $redirect = redirect()->back();
            return $redirect->with('error', "Veuillez entrer un code de réinitialisation valide!");
        }


    }
    public function createUser(Request $r){
        $r->validate([
            'role_id' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|starts_with: +1,+4,+2,+3|min:10|unique:users',
        ]);
        $maaa = "USER-".date('dymis');
        $code = $maaa;

        $input = $r->all();
        $input['statut'] = 0;
        $input['code'] = $code;
        $user = Model::create($input);
        $createHistorique = Models::create([
            'user_code' => $code,
        ]);

        $createValidateHistorique = Model2::create([
            'user_code' => $code,
            'validator' => Auth::user()->code,
        ]);
        $receiver = Auth::user()->id;
        $message_fr = "Un utilisateur vient d'être créé";
        $message_en = "A user has just been created";
        $title_fr = "Création des utilisateurs";
        $title_en = "Creation of users";
        $type ="register-channel";
        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
        $setting = Setting::find(15);
        $lien = $setting->value;
        $massa = Mailes::find(6);
        $details = [
            'subject' => $massa->sujet,
            'header' => $massa->header,
            'btn' => $lien,
            'body' => $massa->contenu.' votre code est '.$code
        ];
        try {
            Mail::to($r['email'])->send(new \App\Mail\Mailer($details));
            return redirect('admin/users')->with([
                'message'    => "Une notification sera envoyée à cet utilisateur!",
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect('admin/users')->with([
                'message'    => "La notification ne sera pas envoyée à cet utilisateur, car son adresse mail est invalide!",
                'alert-type' => 'success',
            ]);
        }
    }

    public function activeUser(Request $r, $users)
    {
        $user = Model::find($r->id);
		$user->statut = 1;
		$user->save();
        $receiver = Auth::user()->id;
        $notification = Notification::create([
            'code' => $user->code,
            'contenu' =>"Nouveau utilisateur activé",
            'contenu_en' =>"New user activated",
            'type' =>3,
            'statut' =>0,
        ]);
        $title_fr = "Activation de compte";
        $title_en = "Activation de compte";
        $message_fr = "Un utilisateur vient d'être activé";
        $message_en = "Un utilisateur vient d'être activé";
        $type ="activation-channel";
        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
        $message2_en = "Your account is activated";
        $message2_fr = "Votre compte vient d'etre activé";
        $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $user->id, $user);
        $this->dispatch(new ActiveUser($user->id)) ;
        return redirect('admin/users')->with([
            'message'    => " Cet utilisateur est maintenant activé!",
            'alert-type' => 'success',
        ]);
    }
    public function deactiveUser(Request $r, $users)
    {
        $user = Model::find($r->id);
        $user_id = Auth::user()->id;
		$user->statut = 0;
		$user->save();
        $message_fr = "Désactivation de compte";
        $message_en = "Account deactivation";
        $type ="deactivation-channel";
        $title_fr = "Désactivation de compte";
        $title_en = "Account deactivation";
        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
        $message2_en = "Your account has been deactivated";
        $message2_fr = "Votre compte vient d'etre désactivé";
        $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $user->id, $user);
        $this->dispatch(new DeactiveUser($user->id));
        $desactivation = DesactivationUserHistorique::create([
            'user_code' => $user->code,
        ]);
        $notification = Notification::create([
            'code' => $user->code,
            'contenu' =>"Nouveau utilisateur désactivé",
            'contenu_en' =>"New user deactivated",
            'type' =>4,
            'statut' =>0,
        ]);
        return redirect('admin/users')->with([
            'message'    => " Cet utilisateur est maintenant désactivé!",
            'alert-type' => 'success',
        ]);
    }
    public function csoUser(Request $r, $users)
    {
        $user = Model::find($r->id);
		$user->role_id = 2;
		$user->save();
        $receiver = Auth::user()->id;
        $message_fr = "Un utilisateur vient d'être rétrogradé CSO";
        $message_en = "A user has just been demoted to CSO";
        $message2_en = "You have just been demoted to CSO";
        $message2_fr = "Vous venez d'être rétrogradé au rang de CSO";
        $title_en = "Assignation de fonction";
        $title_fr = "Assignation of fonction";
        $receiver = Auth::user()->id;
        $type ="downgrade-channel";
        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
        $this->UserPush($message2_fr,$message2_en,$title_fr,$title_en, $user->id, $user);
        return redirect('admin/users')->with([
            'message'    => " Cet utilisateur est maintenant CSO!",
            'alert-type' => 'success',
        ]);
    }
    public function csolUser(Request $r, $users)
    {
        $user = Model::find($r->id);
		$user->role_id = 3;
		$user->save();
        $message_fr = "Un utilisateur vient d'être promu CSOL";
        $message_en = "A user has just been promoted to CSOL";
        $message2_en = "You have just been promoted to CSOL";
        $message2_fr = "Vous venez d'être promu au CSOL";
        $title_fr = "Promotion de CSO";
        $title_en = "Promotion of CSO";
        $receiver = Auth::user()->id;
        $type ="promotion-channel";
        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
        $this->UserPush($message2_fr,$message2_en,$title_fr,$title_en, $user->id, $user);
        $this->dispatch(new PromoteUser($user->id));
        $receiver = Auth::user()->id;

        return redirect('admin/users')->with([
            'message'    => " Cet utilisateur est maintenant CSOL!",
            'alert-type' => 'success',
        ]);
    }

    public function createUserView(){
        return view('voyager::createUser.create');
    }
    public function finishCreateUserView(){
        return view('voyager::createUser.email');
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'all users.xlsx');
    }
    public function export2()
    {
        return Excel::download(new UsersExport, 'all users.pdf');
    }
    public function saveToken (Request $request)
    {
        $user = Model::find($request->user_id);
        $user->device_token = $request->fcm_token;
        $user->save();

        if($user){
            return response()->json([
                'message' => 'User token updated'
            ]);
        }else{
            return response()->json([
                'message' => 'Error!'
            ]);
        }
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
    public function UserPush ($message_fr,$message_en, $title_fr,$title_en, $user, $datas)
    {
        try{
            $recever = Model::where("id", $user)->first();
            $data = [
                "to" => "$recever->device_token",
                "notification" =>
                    [
                        "title" => $title_fr,
                        "body" => $message_fr,
                        "icon" => url('/storage/settings/May2022/8bEb9qzJIvBqOiEjd89r.png')
                    ],
                    "data"=> $datas
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
            // dd(curl_exec($ch));
        } catch (\Throwable $th) {
        }
    }
    public function login(Request $r)
    {
        $r->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        $user = Model::where('email', $r->email)->first();
        if($user){
            if($user->statut == 0 && $user->device_token == null){
                return redirect('admin/login')->with('error', "Your account has not yet been activated");
            }elseif($user->statut == 0 ){
                return redirect('admin/login')->with('error', "Your account is deactivated");
            }else{
                if (Auth::attempt(['email' => $r->email, 'password' => $r->password])) {
                    $user = Auth::user();
                    $token = $user->createToken('AUTH')->accessToken;
                    $user->remember_token = $token;
                    $user->save();
                    return redirect('admin')->with([
                        'message'    => "User logged successfully",
                        'alert-type' => 'success',
                    ]);
                } else {
                    $redirect = redirect()->back();
                    return $redirect->with('error', "Please check your login information!");
                }
            }
        }else{
            return redirect('admin')->with([
                'message'    => "Bad user",
                'alert-type' => 'error',
            ]);
        }
    }
}
