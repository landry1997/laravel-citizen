<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Mailes;
use App\Events\MyEvent;
use App\Jobs\ActiveUser;
use App\Jobs\DeactiveUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User as Model;
use TCG\Voyager\Models\Setting;
use App\Models\OauthAccessToken;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\CreationUserHistorique as Models;
use App\Models\ValidateUserHistorique as Model2;
use App\Models\DesactivationUserHistorique as Model3;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $r->validate([
            'name' => 'required|string',
            'fcm_token' => 'required|string',
            'lastname' => 'required|string',
            'role_id' => 'required|int',
            'email' => 'required|email|unique:users',
            'region' => 'required|int',
            'ville' => 'required|int',
            'phone' => 'required|unique:users|regex:/[0-9]{8}/',
            'password' => 'min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6',
        ]);
        $setting = Setting::find(13);
        $admin = Model::where('role_id', 5)->get();
        $csol = Model::where('role_id', 3)->where('region', $r->region)->get();
        $massa = Mailes::find(7);
        $details = [
            'subject' => $massa->sujet,
            'header' => $massa->header,
            'btn' => 'no',
            'body' => $massa->contenu
        ];

        try {
            Mail::to($r['email'])->send(new \App\Mail\Mailer($details));
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "veuillez entrer une adresse mail valide!",
                'message_en'    => "please enter a valid email address",
            ];
            return response()->json($response);
        }
        $maaa = "USER-".date('dymis');
        $input = $r->all();
        $input['password'] = Hash::make($r->password);
        $input['code'] = $maaa;
        $input['statut'] = 0;
        $utilisateur = Model::create($input);

        $createHistorique = Models::create([
            'user_code' => $maaa,
        ]);

        $title_fr = "Nouvel Utilisateur";
        $title_en = "Nouvel Utilisateur";
        $message_fr = "Un nouvel utilisateur vient de créer son compte.";
        $message_en = "Un nouvel utilisateur vient de créer son compte.";
        $type ="user-registration";
        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $utilisateur, $type);
        $massa = Mailes::find(8);
        $details = [
            'subject' => $massa->sujet,
            'header' => $massa->header,
            'btn' => 'no',
            'body' => $massa->contenu
        ];
        foreach ($admin as $key) {
            try {
                Mail::to($key['email'])->send(new \App\Mail\Mailer($details));
            } catch (\Throwable $th) {
            }
        }
        foreach ($csol as $value) {
            try {
                Mail::to($value['email'])->send(new \App\Mail\Mailer($details));
            } catch (\Throwable $th) {
            }
        }
        $response = [
            'message_fr'    => "Opération réussie!",
            'message_en'    => "Successful operation    !",
            'data' => $utilisateur
        ];
        return response()->json($response);
    }
    public function manageActivation(Request $r, $code)
    {
        $r->validate([
            'type' => 'required',
        ]);
        $user = Model::where('code', $code)->first();
        if($user){
            $validator = Model::where('code', $r->validator)->first();
            if($validator){
                if($validator->role_id != 2){
                    if ($r->type == 0) {
                        $r->validate([
                            'validator' => 'required',
                        ]);
                        $validator = Model::where("code", $r->validator)->first();
                        if($user->statut == 1){
                            $response = [
                                'message_fr'    => "Le compte de cet utilisateur est déja activé!",
                                'message_en'    => "The account of this user is already activated",
                                'data' => $user
                            ];
                            return response()->json($response, 422);
                        }
                        if ($validator->role_id == 5) {
                            $user->statut = 1;
                            $createValidateHistorique = Model2::create([
                                'user_code' => $code,
                                'validator' =>$r->validator,
                            ]);
                            broadcast(new MyEvent($user->id, array("type" => "info", "message" => "Un utilisateur vient d'être activé!", "data" => $user)))->toOthers();
                            $title_fr = "Activation de compte";
                            $title_en = "Account Activation";
                            $message_fr = "Un utilisateur vient d'être activé";
                            $message_en = "A user has just been activated";
                            $type ="activation-channel";
                            $utilisateur = Model::where('code', $code)->first();
                            $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $utilisateur, $type);
                            $message2_fr = "Votre compte vient d'etre activé";
                            $message2_en = "Your account has been activated";
                        
                            $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $r->user_id, $user);
                            $this->dispatch(new ActiveUser($user->id)) ;
                            $notification = Notification::create([
                                'code' => $code,
                                'contenu' =>"Nouveau utilisateur activé",
                                'contenu_en' =>"New user activated",
                                'type' =>3,
                                'statut' =>0,
                            ]);
                            $massa = Mailes::find(2);
                            $details = [
                                'subject' => $massa->sujet,
                                'header' => $massa->header,
                                'btn' => 'no',
                                'body' => $massa->contenu
                            ];
                        } elseif ($validator->role_id == 3 || $validator->region == $user->region) {
                            $user->statut = 1;
                            $createValidateHistorique = Model2::create([
                                'user_code' => $code,
                                'validator' =>$r->validator,
                            ]);
                            broadcast(new MyEvent($user->id, array("type" => "info", "message" => "Un utilisateur vient d'être activé!", "data" => $user->name)))->toOthers();
                            
                            $message_fr = "Un utilisateur vient d'être activé";
                            $message_en = "A user has just been activated";
                            $type ="activation-channel";
                            $title_fr = "Activation de compte";
                            $title_en = "Account Activation";
                            $utilisateur = Model::where('code', $code)->first();
                            $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $utilisateur, $type);
                            $message2_fr = "Votre compte vient d'etre activé";
                            $message2_en = "Your account has been activated";
                            
                            $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $r->user_id, $user);
                            $this->dispatch(new ActiveUser($user->id)) ;
                        }
                    } else {
                        if($user->statut == 0){
                            $response = [
                                'message_fr'    => "Le compte de cet utilisateur est déja désactivé!",
                                'message_en'    => "The account of this user is already deactivated",
                                'data' => $user
                            ];
                            return response()->json($response, 422);
                        }
                        $user->statut = 0;
                        $desactivation = Model3::create([
                            'user_code' => $code,
                            'validator' =>$r->validator,
                        ]);
                        broadcast(new MyEvent($user->id, array("type" => "info", "message" => "Un utilisateur vient d'être désactivé!", "data" => $user)))->toOthers();
                        $message_fr = "Désactivation de compte";
                        $message_en = "Account deactivation";
                        $type ="deactivation-channel";
                        $title_fr = "Désactivation de compte";
                        $title_en = "Account deactivation";
                        $utilisateur = Model::where('code', $code)->first();
                        $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $utilisateur, $type);
                        $message2_fr = "Votre compte vient d'etre désactivé";
                        $message2_en = "Your account has been deactivated";
                        
                        $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $r->user_id, $user);
                        $this->dispatch(new DeactiveUser($user->id));
                        $notification = Notification::create([
                            'code' => $code,
                            'contenu' =>"Nouveau utilisateur désactivé",
                            'contenu_en' =>"New user deactivated",
                            'type' =>4,
                            'statut' =>0,
                        ]);
                    }
                    $user->save();
                    $response = [
                        'message_fr'    => "Opération réussie!",
                        'message_en'    => "Successful operation!",
                        'data' => $user
                    ];
                    return response()->json($response);
                }else{
                    $response = [
                        'message_fr'    => "Vous n'avez pas les autorisations nécessaire pour activer cet utilisateur",
                        'message_en'    => "You do not have the necessary permissions to activate this user",
                        'data' => $user
                    ];
                    return response()->json($response, 422);
                }
            }else{
                $response = [
                    'message_fr'    => "l'utilisateur qui veut effectuer l'action n'existe pas.",
                    'message_en'    => "the user who wants to perform the action does not exist.",
                ];
                return response()->json($response, 422);
            }
        }else{
            $response = [
                    'message_fr'    => "l'utilisateur que vous voulez activer n'existe pas. veuillez choisir un utilisateur valide.",
                    'message_en'    => "the user you want to activate does not exist. please choose a valid user.",
                ];
                return response()->json($response, 422);
        }
    }
    public function userDetail($token){
        $users = Model::where('remember_token',$token)->first();
        if($users){
            return response()->json([
                'message_fr' => 'Utilisateur',
                'message_en' => 'User',
                'user' => $users,
            ]);
        }else{
            $response = [
                'message_fr'    => "Une erreur est survenue l'utilisateur n'éxiste pas!",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
    }
    public function login(Request $r)
    {
        $r->validate([
            'email' => 'email',
            'password' => 'required',
        ]);
        if(!empty($r->email)){
            $user = Model::where('email', $r->email)->first();
        }elseif(!empty($r->phone)){
            $user = Model::where('phone', $r->phone)->first();
        }
        if($user){
            if($user->statut == 1){
                if (Auth::attempt(['email' => $r->email, 'password' => $r->password]) ||  Auth::attempt(['phone' => $r->phone, 'password' => $r->password])) {
                    // The user is active, not suspended, and exists.
                    $user = Auth::user();
                    if($user->fcm_token != Null){
                        $token = $user->createToken('AUTH')->accessToken;
                        $user->remember_token = $token;
                        $user->device_token = $user->fcm_token;
                        $user->code_reset = "";
                        $user->save();
                        $response = [
                            "data"=> [
                                'user'=> $user,
                                'token'=> $token,
                            ],
                            'message_fr' => 'Utilisateur connecté avec succès',
                            'message_en' => 'User logged successfully',
                        ];
                        return response()->json($response, 200);
                    }else{
                        $response = [
                            'message_fr' => 'Vous êtes peut-être un robot',
                            'message_en' => 'You may be a robot',
                        ];
                        return response()->json($response, 422);
                    }
                } else {
                    $response = [
                        'message_fr' => 'Veuillez vérifier vos informations de connexion',
                        'message_en' => 'Please check your login information',
                    ];
                    return response()->json($response, 422);
                }
            }elseif(empty($user->device_token)){
                $response = [
                    'message_fr' => 'Votre compte n\'est pas encore activé',
                    'message_en' => 'Your account is not yet activated',
                ];
                return response()->json($response, 422);
            }else{
                $response = [
                    'message_fr' => 'Votre compte est désactivé',
                    'message_en' => 'Please check if your active',
                ];
                return response()->json($response, 422);
            }
        }else{
            $response = [
                'message_fr' => 'Utilisateur inexistant',
                'message_en' => 'Non-existent user',
            ];
            return response()->json($response, 422);
        }
    }
    public function resetVerificationEmail($email)
    {
        if(!empty($email)){
            $massa = Mailes::find(9);
            $code = date('mdsi');
            $details = [
                'subject' => $massa->sujet,
                'header' => $massa->header,
                'btn' => 'no',
                'body' => $massa->contenu." ".$code
            ];
            if($user = Model::where('email', $email)->first()){
                $user = Model::where('email', $email)->first();
                $user->code_reset = $code;
                $user->save();
                Mail::to($email)->send(new \App\Mail\Mailer($details));
                $response = [
                    'message_fr' => 'Mail de vérification envoyé avec succès.',
                    'message_en' => 'Verification email sent successfully',
                ];
                return response()->json($response, 200);
            }elseif($user = Model::where('phone', $email)->first()){
                $user = Model::where('phone', $email)->first();
                $code = date('mdsi');
                $user->code_reset = $code;
                $user->save();
                Mail::to($user->email)->send(new \App\Mail\Mailer($details));
                $response = [
                    'message_fr' => 'Mail de vérification envoyé avec succès.',
                    'message_en' => 'Verification email sent successfully',
                ];
                return response()->json($response, 200);
            }else{
                $response = [
                    'message_fr' => 'Utilisateur invalide.',
                    'message_en' => 'Invalid user',
                ];
                return response()->json($response, 422);
            }
        }
    }
    public function identify($user_code)
    {
        $users = Model::where('code_reset', $user_code)->first();
        return $users;
        if($users){
            $response = [
            'message_fr'    => "Opération réussie!",
            'message_en'    => "Successful operation!",
            "utilisateur" => $users
        ];
        return response()->json($response, 200);
        }else{
            $response = [
            'message_fr'    => "Veuillez fournir un code valide!",
            'message_en'    => "an error occured please enter et valid code",
        ];
        return response()->json($response, 422);
        }
    }
    public function resetPassword(Request $r)
    {
        $r->validate([
            'code' => 'required',
            'password' => 'min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8',
        ]);
        try {
            $user = Model::where('code_reset', $r->code)->update(array('password' => HASH::make($r->password)));
            $users = Model::where('code_reset', $r->code)->first();
            $users->code_reset = "";
            $users->save();
            $response = [
                'message_fr'    => "Opération réussie!",
                'message_en'    => "Successful operation!",
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "Une erreur est survenue!",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
    }
    public function logout(Request $r)
    {
        $user = OauthAccessToken::where('user_id',$r->id)->orderby('created_at', 'desc')->first();
        $user->revoked = 1;
        $user->save();
        return response()->json([
            'message_fr' => 'LOGOUT',
            'message_en' => 'LOGOUT',
        ]);
    }
    public function refreshToken(Request $r)
    {
        $users = Model::where('remember_token',$r->token)->first();
        // return $users;
        if($users){
            $auth = OauthAccessToken::where('user_id', $users->id)->orderby('created_at', 'desc')->first();
            $token = $users->createToken('AUTH')->accessToken;
            $users->remember_token = $token;
            $users->save();
            $auth->revoked = 1;
            $auth->save();
            return response()->json([
                'message_fr' => 'actualisation de la date d\'expiration du token',
                'message_en' => 'New token expiration date',
                'token' => $users->remember_token,
                'user' => $users,
            ]);
        }else{
            $response = [
                'message_fr'    => "Une erreur est survenue l'utilisateur n'éxiste pas!",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
    }
    public function updateDeviceToken(Request $r)
    {
        $users = Model::where('fcm_token',$r->token)->first();
        if($users){
            $user = Model::find($r->user_id)->update(['device_token' => $r->token]);
            return response()->json([
                'message_fr' => 'device token modifié avec succès',
                'message_en' => 'device token update successfully',
                'token' => $users->fcm_token,
                'user' => $users,
            ]);
        }else{
            $response = [
                'message_fr'    => "Une erreur est survenue l'utilisateur n'éxiste pas!",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
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
    public function UserPush ($message_fr,$message_en, $title_fr,$title_en, $user, $data)
    {
        try{
            $recever = User::find($user);
            $data = [
                "to" => $recever->device_token,
                "notification" =>
                    [
                        "title" => $title_fr,
                        "body" => $message_fr,
                        "title_en" => $title_en,
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
            return $th;
        }
    }
}
