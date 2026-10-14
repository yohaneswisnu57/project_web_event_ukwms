<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParticipantVerifiedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Terverifikasi - Tiket RETINA 2026',
        );
    }

    public function content(): Content
    {
        $qrCode = (string) QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate($this->participant->uuid);

        return new Content(
            markdown: 'emails.participant-verified',
            with: [
                'qrCode' => $qrCode,
            ],
        );
    }
}
