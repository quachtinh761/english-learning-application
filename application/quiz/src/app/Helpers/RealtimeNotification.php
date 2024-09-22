<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class RealtimeNotification
{
    public function send(string $topic, string $title, string $body, array $additionalData = []): void
    {
        Log::info('Push notification realtime', [
            'topic' => $topic,
            'title' => $title,
            'body' => $body,
            'additional_data' => $additionalData
        ]);

        $firebase = (new Factory)
            ->withServiceAccount(base_path('credentials/firebase_credentials.json'));

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'topic' => 'global',
            'data' => $additionalData
        ]);

        $messaging->send($message);
    }
}
