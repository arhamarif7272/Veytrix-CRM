<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    public function __construct(
        protected ?string $apiKey = null,
        protected ?string $fromEmail = null,
        protected ?string $fromName = null
    ) {
        parent::__construct();
        $this->apiKey = $apiKey ?: (string) (config('services.brevo.key') ?? env('BREVO_API_KEY'));
        $this->fromEmail = $fromEmail ?: (string) (config('services.brevo.from_email') ?? env('BREVO_FROM_EMAIL', env('MAIL_FROM_ADDRESS')));
        $this->fromName = $fromName ?: (string) (config('services.brevo.from_name') ?? env('BREVO_FROM_NAME', env('MAIL_FROM_NAME', 'Veytrix')));
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $recipients = [];
        foreach ($email->getTo() as $to) {
            $recipients[] = [
                'email' => $to->getAddress(),
                'name' => $to->getName() ?: null,
            ];
        }

        // Sender fallback
        $senderEmail = $this->fromEmail;
        $senderName = $this->fromName;
        $from = $email->getFrom();
        if (!empty($from)) {
            $senderEmail = $from[0]->getAddress() ?: $this->fromEmail;
            $senderName = $from[0]->getName() ?: $this->fromName;
        }

        $payload = [
            'sender' => [
                'email' => $senderEmail,
                'name' => $senderName,
            ],
            'to' => $recipients,
            'subject' => $email->getSubject() ?: 'Notification from Veytrix',
            'htmlContent' => $email->getHtmlBody() ?: ($email->getTextBody() ? nl2br(htmlspecialchars($email->getTextBody())) : ''),
        ];

        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        // Process attachments & inline images
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                'name' => $attachment->getName() ?: 'attachment',
                'content' => base64_encode($attachment->getBody()),
            ];
        }

        if (!empty($attachments)) {
            $payload['attachment'] = $attachments;
        }

        $response = Http::timeout(30)->withHeaders([
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            Log::error('Brevo API Mail Delivery Failed: ' . $response->body());
            throw new \RuntimeException('Brevo Mail Delivery Error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
