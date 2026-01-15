<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use App\Models\Base\Genesys;
use Illuminate\Mail\Mailables\Address;

class InscriptionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;

    /**
     * Create a new message instance.
     */
    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('hello@wakmessenger.fr', "Madiana"), 
            subject: "Vous êtes inscrit(e) au Marathon de l'horreur !",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inscription-confirmation',
            with: [
                'id' => $this->participant->id,
                'firstname'=> Genesys::Decrypt($this->participant->firstname),
                'lastname'=> Genesys::Decrypt($this->participant->lastname),
                'email' => $this->participant->email ? Genesys::Decrypt($this->participant->email) : null,
            ],
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