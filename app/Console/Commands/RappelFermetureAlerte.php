<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Mailes;
use App\Models\QuickAlerte;
use App\Models\DemandeSuivi;
use App\Models\SimpleAlerte;
use Illuminate\Console\Command;

class RappelFermetureAlerte extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:rappel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send rappel to turn off alert';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $simple_alerte = SimpleAlerte::with(['user'])->where('latitude', '!=', NULL)->where('longitude', '!=', NULL)->get();
        $quick_alerte = QuickAlerte::with(['user'])->where('latitude', '!=', NULL)->where('longitude', '!=', NULL)->get();
        $suivi_demande = DemandeSuivi::with(['user'])->where('latitude', '!=', NULL)->where('longitude', '!=', NULL)->get();
        if(count($simple_alerte) > 0){
            foreach($simple_alerte as $sa){
                $csol = User::where('region', $sa->region)->Where('role_id', 3)->get();
                $admin = User::orWhere('role_id', 5)->get();
                foreach($admin as $ad){
                    $massa = Mailes::find(10);
                    $details = [
                        'subject' => $massa->sujet,
                        'header' => $massa->header,
                        'btn' => 'no',
                        'body' => $massa->contenu
                    ];
                    try {
                        //code...
                        \Mail::to($ad['email'])->send(new \App\Mail\Mailer($details));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
                foreach($csol as $ad){
                    $massa = Mailes::find(10);
                    $details = [
                        'subject' => $massa->sujet,
                        'header' => $massa->header,
                        'btn' => 'no',
                        'body' => $massa->contenu
                    ];
                    try {
                        //code...
                        \Mail::to($ad['email'])->send(new \App\Mail\Mailer($details));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }

            }
        }
        if(count($quick_alerte) > 0){
            foreach($quick_alerte as $sa){
                $csol = User::where('region', $sa->region)->Where('role_id', 3)->get();
                $admin = User::orWhere('role_id', 5)->get();
                foreach($admin as $ad){
                    $massa = Mailes::find(11);
                    $details = [
                        'subject' => $massa->sujet,
                        'header' => $massa->header,
                        'btn' => 'no',
                        'body' => $massa->contenu
                    ];
                    try {
                        //code...
                        \Mail::to($ad['email'])->send(new \App\Mail\Mailer($details));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
                foreach($csol as $ad){
                    $massa = Mailes::find(10);
                    $details = [
                        'subject' => $massa->sujet,
                        'header' => $massa->header,
                        'btn' => 'no',
                        'body' => $massa->contenu
                    ];
                    try {
                        //code...
                        \Mail::to($ad['email'])->send(new \App\Mail\Mailer($details));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }

            }
        }
        if(count($suivi_demande) > 0){
            foreach($suivi_demande as $sa){
                $csol = User::where('region', $sa->region)->Where('role_id', 3)->get();
                $admin = User::orWhere('role_id', 5)->get();
                foreach($admin as $ad){
                    $massa = Mailes::find(11);
                    $details = [
                        'subject' => $massa->sujet,
                        'header' => $massa->header,
                        'btn' => 'no',
                        'body' => $massa->contenu
                    ];
                    try {
                        //code...
                        \Mail::to($ad['email'])->send(new \App\Mail\Mailer($details));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
                foreach($csol as $ad){
                    $massa = Mailes::find(10);
                    $details = [
                        'subject' => $massa->sujet,
                        'header' => $massa->header,
                        'btn' => 'no',
                        'body' => $massa->contenu
                    ];
                    try {
                        //code...
                        \Mail::to($ad['email'])->send(new \App\Mail\Mailer($details));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }

            }
        }
        return Command::SUCCESS;
    }
}
