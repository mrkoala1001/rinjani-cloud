<?php

namespace App\Mail\Depootcom;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('[Depootcom] Kontak dari: ' . $this->data['subject'])
                    ->html("
                        <h3>Pesan Baru dari depootcom.site</h3>
                        <p><strong>Nama:</strong> {$this->data['name']}</p>
                        <p><strong>Email:</strong> {$this->data['email']}</p>
                        <p><strong>Subjek:</strong> {$this->data['subject']}</p>
                        <hr>
                        <p><strong>Pesan:</strong></p>
                        <p>{$this->data['message']}</p>
                    ");
    }
}
