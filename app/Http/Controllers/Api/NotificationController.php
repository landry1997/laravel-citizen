<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification as Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function userNotification($user, $page)
    {
        $notifications = Model::where("user_id",$user)->paginate(15, ['*'], 'page', $page)->items();
        return $this->sendResponse($notifications, 'Notifications recupérées avec succès !', 'Notifications get successfully !');
    }
}
