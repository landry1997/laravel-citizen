<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Mailes;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class MakeSimpleAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var int
     */
    private $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
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
        $admin = User::whereIn('role_id', [5,4])->where('email','<>','admin@admin.com')->get();
        $csol = User::where('role_id', 3)->where('region', $this->user)->get();
        $massa = Mailes::find(13);
        $details = [
            'subject' => $massa->sujet,
            'header' => $massa->header,
            'btn' => 'no',
            'body' => $massa->contenu
        ];
        // return $this->admin;
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
    }
}
