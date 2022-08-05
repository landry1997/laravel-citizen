<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DesinstallationController;

/*
|--------------------------------------------------------------------------
| API Routesf
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
        // user management

            
        Route::get('identify/{user_code}', [App\Http\Controllers\Api\AuthController::class, 'identify']);
            
            Route::get('allAlerte', [App\Http\Controllers\Api\AlerteController::class, 'allAlerte']);
        Route::get('terms', [App\Http\Controllers\ViewController::class, 'terms'])->name('terms');
        Route::post('finishCreateUser', [App\Http\Controllers\Api\UserController::class, 'finishCreateUser']);
        
        //auth system
        Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('makeAlert', [App\Http\Controllers\Api\AlerteController::class, 'makeAlert']);
        
        Route::middleware('auth:api')->group( function () {
            Route::get('allUsers', [App\Http\Controllers\Api\UserController::class, 'allUsers']);
        Route::post('promoteCsoUser/{code}', [App\Http\Controllers\Api\UserController::class, 'promoteCsoUser']);
        
            Route::post('manageActivation/{code}', [App\Http\Controllers\Api\AuthController::class, 'manageActivation']);
        Route::post('fermetureAlertSuivi', [App\Http\Controllers\Api\AlerteController::class, 'fermetureAlertSuivi']);
        
        
            Route::get('allAlerteByUser/{user_id}', [App\Http\Controllers\Api\AlerteController::class, 'allAlerteByUser']);
        Route::post('retrogradeCsoUser/{code}', [App\Http\Controllers\Api\UserController::class, 'retrogradeCsoUser']);
            // user management
            Route::post('activeUser/{code}', [App\Http\Controllers\Api\UserController::class, 'activeUser']);
            Route::post('deactiveUser/{code}', [App\Http\Controllers\Api\UserController::class, 'deactiveUser']);
            Route::post('createUser', [App\Http\Controllers\Api\UserController::class, 'createUser']);


            Route::post('makeAlerteSuivi', [App\Http\Controllers\Api\AlerteController::class, 'makeAlerteSuivi']);

            Route::post('request_desinstallation', [App\Http\Controllers\Api\DesinstallationController::class, 'request_desinstallation']);



            Route::get('get-user-notifications/{user}/{page}', [NotificationController::class, 'userNotification']);

            // alert and demand management
            Route::get('alerteByUser/{user_id}/{type}', [App\Http\Controllers\Api\AlerteController::class, 'alerteByUser']);
            Route::get('allPosition/{suivi_id}', [App\Http\Controllers\Api\SuiviPositionController::class, 'allPosition']);
            Route::get('detailAlert/{code}/{type}', [App\Http\Controllers\Api\AlerteController::class, 'detailAlert']);
            Route::get('lastPosition/{user_id}', [App\Http\Controllers\Api\SuiviPositionController::class, 'lastPosition']);
            // historique
            Route::get('creation_historique', [App\Http\Controllers\Api\HystoryController::class, 'creation_historique']);
            Route::get('validation_historique', [App\Http\Controllers\Api\HystoryController::class, 'validation_historique']);
            Route::get('desinstallation_historique', [App\Http\Controllers\Api\HystoryController::class, 'desinstallation_historique']);
            // exports
            Route::get('users-export-excel', [App\Http\Controllers\Web\UserController::class, 'export']);
            Route::get('users-export-pdf', [App\Http\Controllers\Web\UserController::class, 'export2']);
            Route::get('all-follow-up-request-export-excel', [App\Http\Controllers\Web\AlerteController::class, 'allDemandeSuivi']);
            Route::get('all-follow-up-request-export-pdf', [App\Http\Controllers\Web\AlerteController::class, 'allDemandeSuivi2']);
            Route::get('all-quick-alert-export-excel', [App\Http\Controllers\Web\AlerteController::class, 'allQuickAlerte']);
            Route::get('all-quick-alert-export-pdf', [App\Http\Controllers\Web\AlerteController::class, 'allQuickAlerte2']);
            Route::get('all-simple-alert-export-excel', [App\Http\Controllers\Web\AlerteController::class, 'allSimpleAlerte']);
            Route::get('all-simple-alert-export-pdf', [App\Http\Controllers\Web\AlerteController::class, 'allSimpleAlerte2']);
            Route::post('last-positions-of-user-excel', [App\Http\Controllers\Web\SuiviPositionController::class, 'suivi_position_user']);
            Route::post('last-positions-of-user-pdf', [App\Http\Controllers\Web\SuiviPositionController::class, 'suivi_position_user2']);

            Route::get('all-position-of-suivi-demand-export-excel', [App\Http\Controllers\Web\SuiviPositionController::class, 'suivi_position']);
            Route::get('all-position-of-suivi-demand-export-pdf', [App\Http\Controllers\Web\SuiviPositionController::class, 'suivi_position2']);

            Route::get('userDetail/{token}', [App\Http\Controllers\Api\AuthController::class, 'userDetail']);
            Route::get('passwordUser/{token}', [App\Http\Controllers\Api\AuthController::class, 'passwordUser']);

            //active and deactive user

            // update user

    });
            Route::post('updateUser', [App\Http\Controllers\Api\UserController::class, 'updateUser']);
    
            
        Route::post('suiviPosition', [App\Http\Controllers\Api\SuiviPositionController::class, 'suiviPosition']);
        Route::get('frequence', [App\Http\Controllers\Api\UserController::class, 'frequence']);
        Route::get('liste-des-villes', [App\Http\Controllers\Api\VilleController::class, 'all_ville']);
        Route::get('liste-des-regions', [App\Http\Controllers\Api\RegionController::class, 'all_region']);
        Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('refreshToken', [App\Http\Controllers\Api\AuthController::class, 'refreshToken']);
        Route::post('updateDeviceToken', [App\Http\Controllers\Api\AlerteController::class, 'updateDeviceToken']);
        Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logOut']);
        Route::post('resetVerificationEmail/{mail}', [App\Http\Controllers\Api\AuthController::class, 'resetVerificationEmail']);
        Route::post('resetPassword', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);


    });

    Route::post('saveToken', [App\Http\Controllers\Web\UserController::class, 'saveToken'])->name('saveToken');
