<?php

// use TCG\Voyager\Facades\Voyager;
use App\Models\User as Model;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserController;
use App\Http\Middleware\EnsureUserActive;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});
Route::get('/', function () {
    \App::setlocale(\Session::get('locale'));
    \Session::put('locale', \Session::get('locale'));
    if (\Auth::user()) {
        Model::find(\Auth::user()->id)->update(['settings' => json_encode(["locale"=>\Session::get('locale')])]);
    }
    return redirect('/admin');
});
Route::post('connexion', [App\Http\Controllers\Web\UserController::class, 'login'])->name('users.log');
Route::patch('/fcm-token', [HomeController::class, 'updateToken'])->name('fcmToken');
Route::group(['prefix' => 'admin'], function () {
    Route::middleware('active')->group( function () {

        Route::get('locale/{locales}', [App\Http\Controllers\LanguageController::class, 'index'])->name('change-language-admin');
        Route::post('retrograder-des-utilisateurs/{id}', [App\Http\Controllers\Web\UserController::class, 'csoUser'])->name('users.csoUser');
        Route::post('promouvoir-des-utilisateurs/{id}', [App\Http\Controllers\Web\UserController::class, 'csolUser'])->name('users.csolUser');
        Route::get('activation-des-utilisateurs/{id}', [App\Http\Controllers\Web\UserController::class, 'activeUser'])->name('users.activeUser');
        Route::get('désactivation-des-utilisateurs/{id}', [App\Http\Controllers\Web\UserController::class, 'deactiveUser'])->name('users.deactiveUser');

        Route::get('désinstallation-apk/{code}', [App\Http\Controllers\Web\DesinstallationController::class, 'manage_desinstallation'])->name('manage.desinstallation');
        Route::get('uninstallation-apk/{code}', [App\Http\Controllers\ViewController::class, 'singlemap'])->name('desinstalation.single');

        Route::post('femeture-tout-type-alerte', [App\Http\Controllers\Web\AlerteController::class, 'fermetureAlertSuivi'])->name('alerte.fermeture');
        Route::get('read-notification/{id}', [App\Http\Controllers\Web\NotificationReadController::class, 'readNotification'])->name('notif.read');

        Route::get('createUserView', [App\Http\Controllers\Web\UserController::class, 'createUserView'])->name('users.createUserView');
        Route::get('all-notifications', [App\Http\Controllers\ViewController::class, 'all_notifications'])->name('notif.all');
        Route::get('finishCreateUserView', [App\Http\Controllers\Web\UserController::class, 'finishCreateUserView'])->name('users.finishCreateUserView');
        Route::post('createUser', [App\Http\Controllers\Web\UserController::class, 'createUser'])->name('users.createUser');
        Route::post('finishCreateUser', [App\Http\Controllers\Web\UserController::class, 'finishCreateUser'])->name('users.finishCreateUser');



        Route::get('map-for-demande-suivi', [App\Http\Controllers\ViewController::class, 'suivi_demand'])->name('demande.map');
        Route::get('map-for-quick-alert', [App\Http\Controllers\ViewController::class, 'quick_alert'])->name('quick.map');
        Route::get('map-for-simple-alert', [App\Http\Controllers\ViewController::class, 'simple_alert'])->name('simple.map');

        Route::get('users-export-excel', [App\Http\Controllers\Web\UserController::class, 'export'])->name('users.export');
        Route::get('users-export-pdf', [App\Http\Controllers\Web\UserController::class, 'export2'])->name('users.export2');
        Route::get('all-follow-up-request-export-excel', [App\Http\Controllers\Web\AlerteController::class, 'allDemandeSuivi'])->name('suivi.all');
        Route::get('all-follow-up-request-export-pdf', [App\Http\Controllers\Web\AlerteController::class, 'allDemandeSuivi2'])->name('suivi.all2');
        Route::get('all-quick-alert-export-excel', [App\Http\Controllers\Web\AlerteController::class, 'allQuickAlerte'])->name('quick.all');
        Route::get('all-quick-alert-export-pdf', [App\Http\Controllers\Web\AlerteController::class, 'allQuickAlerte2'])->name('quick.all2');
        Route::get('all-simple-alert-export-excel', [App\Http\Controllers\Web\AlerteController::class, 'allSimpleAlerte'])->name('simple.all');
        Route::get('all-simple-alert-export-pdf', [App\Http\Controllers\Web\AlerteController::class, 'allSimpleAlerte2'])->name('simple.all2');
        Route::get('all-position-of-suivi-demand-export-excel', [App\Http\Controllers\Web\SuiviPositionController::class, 'suivi_position'])->name('suivi.position');
        Route::get('all-position-of-suivi-demand-export-pdf', [App\Http\Controllers\Web\SuiviPositionController::class, 'suivi_position2'])->name('suivi.position2');

        Route::get('cso', [App\Http\Controllers\ViewController::class, 'cso'])->name('user.cso');
        Route::get('csol', [App\Http\Controllers\ViewController::class, 'csol'])->name('user.csol');
        Voyager::routes();


    });
});

Route::get('/push-notificaiton', [App\Http\Controllers\WebNotificationController::class, 'index'])->name('push-notificaiton');
Route::post('/store-token', [App\Http\Controllers\WebNotificationController::class, 'storeToken'])->name('store.token');
Route::patch('/fcm-token', [App\Http\Controllers\WebNotificationController::class, 'updateToken'])->name('fcmToken');
Route::post('/send-web-notification', [App\Http\Controllers\WebNotificationController::class, 'sendWebNotification'])->name('send.web-notification');
Auth::routes();
Route::post('forgot-password', [App\Http\Controllers\Web\UserController::class, 'forgotPassword'])->name('users.forgot');
Route::post('reset-password-finaly', [App\Http\Controllers\Web\UserController::class, 'resetPwd'])->name('users.resetPwd');
Route::get('reset-password-view', [App\Http\Controllers\Web\UserController::class, 'resetPwdView'])->name('users.reset');
// Route::get('resete-passworde', [App\Http\Controllers\ControllerController::class, 'resete'])->name('users.reset');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::post('sendPush', [App\Http\Controllers\Web\UserController::class, 'sendPush'])->name('sendPush');

Route::get('terms', [App\Http\Controllers\ViewController::class, 'terms'])->name('terms');

Route::post('saveToken', [App\Http\Controllers\Web\UserController::class, 'saveToken'])->name('saveToken');

