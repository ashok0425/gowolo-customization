<?php

namespace App\Mail;

use App\Models\CustomizationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CustomizationRequest $customizationRequest,
        public string $eventType,        // 'new' | 'status_changed'
        public ?string $oldStatus = null,
        public ?string $newStatus = null
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->eventType === 'new'
            ? "New Customization Request — {$this->customizationRequest->ref_number}"
            : "Status Updated — {$this->customizationRequest->ref_number}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.request_notification',
            with: [
                'request'   => $this->customizationRequest,
                'eventType' => $this->eventType,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ],
        );
    }
}
