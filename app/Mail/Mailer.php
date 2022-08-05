<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $det = $this->details;
        $mailToSend =  $this->subject($det['subject'])
                    ->view('mail.mailer',compact('det'));
        // if ($det['attach']) {
        //     foreach($det['attach'] as $filePath){
        //         $mailToSend->attach($filePath['file'], [
        //             'as' => $filePath['name'],
        //         ]);
        //     }
        // }
        return $mailToSend;
    }
}
