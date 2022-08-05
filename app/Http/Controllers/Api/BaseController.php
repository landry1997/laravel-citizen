<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Models\Notification;
use App\Models\User;
use App\Events\MessageSent;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message_fr, $message_en)
    {
    	$response = [
            'type' => "success",
            'data'    => $result,
            'message_fr' => $message_fr,
            'message_en' => $message_en,
        ];
        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($data, $message_fr, $message_en, $code = 422)
    {
    	$response = [
            'type' => "error",
            'data' => $data,
            'message_fr' => $message_fr,
            'message_en' => $message_en,
        ];

        return response()->json($response, $code);
    }

    /**
     * Send push notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_notification_FCM($notification_id, $title, $message, $data, $user_id, $message_en) {

        $accesstoken = "key=AAAAsi2vEVk:APA91bHw2Lp4aRpfokwPYOZItInTx52E2KGfFF74vjgpoO4VS41_nRG5OvyoJqQZtoalyY5CRTi58aEu0g-CV027A5Spj1AUNowMFzRKe7uyH5fdiNP-aKRvaHTtUZJ0p_FstR7EsMx8";

        $URL = 'https://fcm.googleapis.com/fcm/send';


        $post_data = '{
            "to" : "' . $notification_id . '",
            "data" : {
                "message" : "' . $data . '",
            },
            "notification" : {
                "body" : "' . $message . '",
                "title" : "' . $title . '"
            },
        }';

        $crl = curl_init();

        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: ' . $accesstoken;
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);

        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

        $rest = curl_exec($crl);

        if ($rest === false) {
            // throw new Exception('Curl error: ' . curl_error($crl));
            //print_r('Curl error: ' . curl_error($crl));
            $result_noti = 0;
        } else {

            $result_noti = 1;
            $notification = Notification::create([
                "user_id" => $user_id,
                "notif_to" => $user_id,
                "contenu" => $message,
                "contenu_en" => $message_en,
                "donnee" => json_encode($data),
                "type" => "user",
                "statut" => 1
            ]);
        }

        //curl_close($crl);
        //print_r($result_noti);die;
        return $result_noti;
    }

    /**
     * Send admin notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_notification_to_admin($user_id, $message, $message_en, $data, $lien) {
        // $admins = User::
        $notification = Notification::create([
            "user_id" => $user_id,
            "notif_to" => 1,
            "contenu" => $message,
            "contenu_en" => $message_en,
            "lien" => $lien,
            "donnee" => json_encode($data),
            "type" => "admin",
            "statut" => 1
        ]);
        $admins = User::where('role_id',4)->where('statut',1)->get();
        foreach ($admins as $key => $value) {
            $details = [
                'subject' => $message,
                'header' => 'Hello '.$value->name,
                'btn' => $lien,
                'body' => $message
            ];

            try {
                \Mail::to($value->email)->send(new \App\Mail\Mailer($details));
            } catch (\Exception $e) {

            }
        }
        // event(new MessageSent($notification));
        return $notification;
    }
}
