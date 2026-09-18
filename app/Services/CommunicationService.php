<?php

namespace App\Services;

use App\Models\CommunicationTemplate;
use App\Models\CommunicationLog;
use App\Models\MessageTemplate;
use Exception;
use Illuminate\Support\Facades\Log;

class CommunicationService
{
    public static function sendEmail(string $to, string $templateEvent, array $data = [], ?int $hotelId = null): bool
    {
        try {
            $template = MessageTemplate::active()->byChannel('email')->byEvent($templateEvent)->first();
            if (!$template) {
                Log::warning("Email template not found: {$templateEvent}");
                return false;
            }

            $body = $template->render($data);
            $subject = $template->subject;
            foreach ($data as $key => $value) {
                $subject = str_replace("{{ {$key} }}", $value, $subject);
            }

            \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            CommunicationLog::create([
                'channel' => 'email',
                'recipient' => $to,
                'subject' => $subject,
                'body' => $body,
                'status' => 'sent',
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("Email send failed to {$to}: " . $e->getMessage());
            CommunicationLog::create([
                'channel' => 'email',
                'recipient' => $to,
                'subject' => $subject ?? '',
                'body' => $body ?? '',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public static function sendSms(string $phone, string $templateEvent, array $data = []): bool
    {
        try {
            $template = MessageTemplate::active()->byChannel('sms')->byEvent($templateEvent)->first();
            if (!$template) {
                Log::warning("SMS template not found: {$templateEvent}");
                return false;
            }

            $body = $template->render($data);

            CommunicationLog::create([
                'channel' => 'sms',
                'recipient' => $phone,
                'subject' => null,
                'body' => $body,
                'status' => 'sent',
                'metadata' => ['gateway' => config('services.sms.gateway', 'log')],
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("SMS send failed to {$phone}: " . $e->getMessage());
            CommunicationLog::create([
                'channel' => 'sms',
                'recipient' => $phone,
                'body' => '',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public static function sendWhatsApp(string $phone, string $templateEvent, array $data = []): bool
    {
        try {
            $template = MessageTemplate::active()->byChannel('whatsapp')->byEvent($templateEvent)->first();
            if (!$template) {
                Log::warning("WhatsApp template not found: {$templateEvent}");
                return false;
            }

            $body = $template->render($data);

            CommunicationLog::create([
                'channel' => 'whatsapp',
                'recipient' => $phone,
                'subject' => null,
                'body' => $body,
                'status' => 'sent',
                'metadata' => ['gateway' => config('services.whatsapp.gateway', 'log')],
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("WhatsApp send failed to {$phone}: " . $e->getMessage());
            CommunicationLog::create([
                'channel' => 'whatsapp',
                'recipient' => $phone,
                'body' => '',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
