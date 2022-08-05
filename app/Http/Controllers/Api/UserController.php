<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Mailes;
use App\Events\MyEvent;
use App\Jobs\PromoteUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User as Model;
use TCG\Voyager\Models\Setting;
use App\Models\OauthAccessToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\CreationUserHistorique;
use App\Models\CreationUserHistorique as Models;
use App\Models\ValidateUserHistorique as Model2;

class UserController extends Controller
{
    public function createUser(Request $r){
        $r->validate([
            'role_id' => 'required',
            'validator' => 'required',
            'email' => 'required|string|email|max:255|unique:users'
        ]);
        // dd($r->role_id);
        $maaa = "USER-".date('dymis');
        $code = $maaa;
        try {
            $user = Model::create([
                'email' => $r->email,
                'role_id' => $r->role_id,
                'code' => $code,
                'region' => 1,
                'ville' => 1,
                'statut' => 0,
            ]);
            $createHistorique = Models::create([
                'user_code' => $code,
            ]);

            $createValidateHistorique = Model2::create([
                'user_code' => $code,
                'validator' =>$r->validator,
            ]);
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
        broadcast(new MyEvent(Auth::user()->id, array("type" => "info", "message" => "Un nouvel utilisateur vient d'être créé par un administrateur!", "data" => $r->email)))->toOthers();
        $setting = Setting::find(15);
        $lien = $setting->value;
        try {
            $details = [
                'subject' => 'Inscription au panel de MAPME',
                'header' => 'Hello',
                'btn' => $lien,
                'body' => 'Veuillez cliquer sur le liens suivant pour terminer votre inscription sur la plateforme MAPME. votre code de validation est: <strong>'.$code.'</strong>',
            ];
            Mail::to($r['email'])->send(new \App\Mail\Mailer($details));
            $response = [
                'type' => "success",
                'message_fr' => "Utilisateur créé avec succès",
                'message_en' => 'User successfully created',
                'sub_message' => [
                    'sub_message_fr'=> "Le nouvel utilisateur recevra une notification de bienvenue!",
                    'sub_message_en'=> "The new user will receive a welcome notification",
                ],
                "data"=> $user,
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'type' => "error",
                'message_fr' => "Utilisateur créé avec succès",
                'message_en' => 'User successfully created',
                'sub_message' => [
                    'sub_message_fr'=> "Mais malheureusement il ne recevra pas de mail. Veuillez l'activer via le admin!",
                    'sub_message_en'=> "But unfortunately it will not receive any mail. Please activate it via admin",
                ],
                "data"=> $user,
            ];
            return response()->json($response, 200);
        }
    }

    public function retrogradeCsoUser(Request $r, $code)
    {
        $user = Model::where('code',$code)->first();
        if($user){
            if($user->role_id == 2){

                $response = [
                    'type' => "success",
                    'message_fr' => "Cet utilisateur est déjà CSO",
                    'message_en' => 'This user is already a CSO',
                    "data"=> $user,
                ];
                return response()->json($response, 422);
            }else{
                $user->role_id = 2;
                $user->save();
                $message_fr = "Un utilisateur vient d'être rétrogradé CSO";
                $message_en = "A user has just been demoted to CSO";
                $message2_fr = "You have just been demoted to CSO";
                $message2_en = "Vous venez d'etre retrogradé CSO";
                $title_en = "Assignation de fonction";
                $title_fr = "Assignation de fonction";
                $receiver = $user->id;
                $type ="downgrade-channel";
                $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
                $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $user->id, $user);
                broadcast(new MyEvent($user->id, array("type" => "info", "message" => "A user has just been demoted to CSO!", "data" => $user->name)))->toOthers();
                $this->dispatch(new PromoteUser($user->id));
                $response = [
                    'type' => "success",
                    'message_fr' => "Opération réussie",
                    'message_en' => 'Successful operation',
                    "data"=> $user,
                ];
                return response()->json($response, 200);
            }
        }else{
            $response = [
                'type' => "success",
                'message_fr' => "Opération réussie",
                'message_en' => 'Successful operation',
                "data"=> $user,
            ];
            return response()->json($response, 422);
        }
    }
    public function promoteCsoUser(Request $r, $users)
    {
        $user = Model::where('code',$r->code)->first();
        if($user){
            if($user->role_id == 3){
                $response = [
                    'type' => "success",
                    'message_fr' => "Cet utilisateur est déjà CSOL",
                    'message_en' => 'This user is already CSOL',
                    "data"=> $user,
                ];
                return response()->json($response, 422);
            }else{
                $user->role_id = 3;
                $user->save();
                broadcast(new MyEvent($user->id, array("type" => "info", "message" => "A user has just been promoted to CSO Leader!", "data" => $user->name)))->toOthers();
                $message_fr = "Un utilisateur vient d'être promu CSOL";
                $message_en = "A user has just been promoted to CSOL";
                $message2_fr = "You have just been promoted to CSOL";
                $message2_en = "Vous venez juste d'etre promu CSOL";
                $title_fr = "Promotion de CSO";
                $title_en = "Promotion of CSO";
                $receiver = $user->id;
                $type ="promotion-channel";
                $this->sendPush($message_fr,$message_en, $title_fr,$title_en, $user, $type);
                $this->UserPush($message2_fr,$message2_en, $title_fr,$title_en, $user->id, $user);
                $this->dispatch(new PromoteUser($user->id));
                $response = [
                    'type' => "success",
                    'message_fr' => "Opération réussie",
                    'message_en' => 'Successful operation',
                    "data"=> $user,
                ];
                return response()->json($response);
            }
        }else{
            $response = [
                'message_fr'    => "une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }
    }

    public function activeUser(Request $r, $code)
    {
        try {
            $user = Model::where('code',$code)->first();
            $user->statut = 1;
            $user->save();
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }

        $createValidateHistorique = Model2::create([
            'user_code' => $code,
            'validator' =>$r->validator,
        ]);

        broadcast(new MyEvent(Auth::user()->id, array("type" => "info", "message" => "Un utilisateur vient d'être activé!", "data" => $user->name)))->toOthers();
        try {
            $details = [
                'subject' => 'Bienvenue sur COMAID',
                'header' => 'Hello '.$user->name,
                'btn' => 'no',
                'body' => 'Bienvenue sur la plateforme COMAID, vous venez de valider votre compte.'
            ];
            Mail::to($user['email'])->send(new \App\Mail\Mailer($details));
        } catch (\Exception $e) {

        }
        $response = [
            'message_fr' => "Opération réussie",
            'message_en' => 'Successful operation',
            "data"=> $user,
        ];
        return response()->json($response);
    }
    public function deactiveUser($code)
    {
        try {
            $user = Model::where('code',$code)->first();
            $user->statut = 0;
            $user->save();
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }


        broadcast(new MyEvent(Auth::user()->id, array("type" => "info", "message" => "Un utilisateur vient d'être désactivé!", "data" => $user->name)))->toOthers();
        try {
            $details = [
                'subject' => 'Bienvenue sur COMAID',
                'header' => 'Hello '.$user->name,
                'btn' => 'no',
                'body' => 'Bienvenue sur la plateforme COMAID, vous venez de valider votre compte.'
            ];
            Mail::to($user['email'])->send(new \App\Mail\Mailer($details));
        } catch (\Exception $e) {

        }
        $response = [
            'message_fr' => "Opération réussie",
            'message_en' => 'Successful operation',
            "data"=> $user,
        ];
        return response()->json($response);
    }
    public function finishCreateUser(Request $r){
        $r->validate([
            'name' => 'required',
            'password' => 'min:8',
            'password_confirmation' => 'required_with:password|same:password',
            'phone' => 'required',
            'region' => 'required',
            'code' => 'required',
            'ville' => 'required',
        ]);
        $password = Hash::make($r->password);
        $input = $r->all();
        $input['password'] = $password;
        $input['statut'] = 1;
        try {
            $user = Model::where('code', $r->code)->first();
            $user->fill($input)->save();
        } catch (\Throwable $th) {
            $response = [
                'message_fr'    => "une erreur est survenue",
                'message_en'    => "an error occured",
            ];
            return response()->json($response, 422);
        }

        broadcast(new MyEvent(Auth::user()->id, array("type" => "info", "message" => "Un utilisateur vient de compléter ses informations de profil!", "data" => $user->name)))->toOthers();
        try {
            $details = [
                'subject' => 'Bienvenue chez MAPME',
                'header' => 'Hello',
                'btn' => 'no',
                'body' => '<strong>Bienvenue</strong>.<br>Vous venez de rejoindre la grande communauté MAPME',
            ];
            Mail::to($user['email'])->send(new \App\Mail\Mailer($details));
            $response = [
                'type' => "success",
                'message_fr' => "Opération réussie",
                'message_en' => 'Successful operation',
                'sub_message' => [
                    'sub_message_fr'=> "Vous pouvez maintenant vous connecter!",
                    'sub_message_en'=> "You can now connect",
                ],
                "data"=> $user,
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'type' => "success",
                'message_fr' => "Opération réussie",
                'message_en' => 'Successful operation',
                'sub_message' => [
                    'sub_message_fr'=> "Vous ne recevrez pas de mail car votre adresse mail est invalide!",
                    'sub_message_en'=> "You will not receive an email because your email address is invalid",
                ],
                "data"=> $user,
            ];
            return response()->json($response, 200);
        }


    }

    public function updateUser(Request $r){
        $r->validate([
            'name' => 'string',
            'fcm_token' => 'string',
            'email' => 'email:rfc,dns',
            'lastname' => 'string',
            'role_id' => 'int',
            'region' => 'int',
            'token' => 'required',
            'ville' => 'int',
            'phone' => 'unique:users|regex:/[0-9]{8}/',
            'password_confirmation' => 'same:password|min:8',
        ]);
        $users = Model::where('remember_token',$r->token)->orderby('created_at', 'desc')->first();

        if($users){
            if(!empty($r->statut)){

            }
            $input = $r->all();
            if(!empty($r->password)){
                $password = Hash::make($r->password);
                if(Hash::check($r->old_password, $users->password)){
                    $input['password'] = $password;
                }else{
                    $response = [
                        'message_fr'    => "Vous devez entrer votre ancien mot de passe car il n'est pas correct",
                        'message_en'    => "You must enter your old password because it is not correct",
                    ];
                    return response()->json($response, 422);
                }
            }

            // return $users->password;
            if(!empty($r->avatar)){
                $input['avatar'] = \Storage::disk('public')->putFile('users', $r->file('avatar'));
            }
            if(!empty($r->email)){
                if($r->email != $users->email){
                    $input["email"] = $r->email;
                    $users->fill($input)->save();
                    $user = OauthAccessToken::where('user_id',$users->id)->orderby('created_at', 'desc')->first();
                    $user->revoked = 1;
                    $user->save();
                }else{
                    unset($input["email"]);
                    $users->fill($input)->save();
                }
            }
            $user = Model::where('remember_token',$r->token)->first();
            $response = [
                'message_fr'    => "Données modifiées avec succès",
                'message_en'    => "Successful operation",
                'user'    => $user
            ];
            return response()->json($response, 200);
        }else{
            $response = [
                'message_fr'    => "Utilisateur invalide",
                'message_en'    => "Bad user",
            ];
            return response()->json($response, 422);
        }
    }

    public function allUsers(){

        $active = User::where('statut', 1)->where('name','<>', null)->whereIn('role_id', [2,3])->where('role_id','<>', 6)->get();
        $deactive = User::where('statut','<>', 1)->where('name','<>', null)->whereIn('role_id', [2,3])->where('role_id','<>', 6)->get();
        $response = [
            'message_fr' => "All creations users",
            'message_en' => 'Successful operation',
            "active_users"=> $active,
            "deactive_users"=> $deactive,
        ];
        return response()->json($response, 200);
    }

    public function frequence(){
        try {
            //code...
            $frequent = \DB::table('settings')->where('key', 'admin.frequence_tracking')->first();
            $response = [
                "frequence"=> $frequent,
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            //throw $th;
            $response = [
                "message"=> "erreur innatendue",
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
            $recever = Model::find($user);
            $data = [
                "to" => "$recever->device_token",
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
        }
    }
}
