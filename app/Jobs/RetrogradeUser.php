<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Mailes;
use TCG\Voyager\Models\Setting;

class RetrogradeUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recever = User::find($this->user);
        $setting = Setting::find(13);
        $lien = $setting->value;
        $massa = Mailes::find(4);
        $details = [
            'subject' => $massa->sujet,
            'header' => $massa->header,
            'btn' => $lien,
            'body' => $massa->contenu
        ];
        Mail::to($recever['email'])->send(new \App\Mail\Mailer($details));
    }
}
