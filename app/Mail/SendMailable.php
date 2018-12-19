<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $token;
    public $type;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $token, $type)
    {
        $this->name = $name;
        $this->token = $token;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Account confirmation")->view('emails.welcome');
    }
}
