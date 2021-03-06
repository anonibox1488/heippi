<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class resetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Cambiar contraseña';
    public $datos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($correo)
    {
        $this->datos = $correo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.resetpassword');
    }
}
