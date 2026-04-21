<?php

namespace App\Mail;

use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeviceOfflineAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Device $device,
        public readonly int $minutesOffline,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[ALERT] Perangkat '{$this->device->name}' Offline — RFID Attendance",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.device-offline',
        );
    }
}
