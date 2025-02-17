<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

   
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contacts Import Completed',
        );
    }

  
    public function content(): Content
    {
        return new Content(
            view: 'emails.import_completed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}