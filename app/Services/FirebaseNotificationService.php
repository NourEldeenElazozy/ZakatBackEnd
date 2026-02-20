<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging; // تأكد من استيراد هذا

class FirebaseNotificationService
{
    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Sends a notification to a specific device token.
     */
    public function sendNotificationToDevice(string $deviceToken, string $title, string $body, array $data = []): void
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($data); // يمكن تمرير بيانات إضافية اختيارياً

        try {
            $this->messaging->send($message);
            \Log::info("Notification sent successfully to device token: {$deviceToken}");
        } catch (\Exception $e) {
            \Log::error("Failed to send notification to device token {$deviceToken}: " . $e->getMessage());
            throw $e; // يمكنك اختيار إعادة رمي الاستثناء أو التعامل معه بشكل مختلف
        }
    }

    /**
     * Sends a notification to a specific topic.
     */
    public function sendNotificationToTopic(string $topic, string $title, string $body, array $data = []): void
    {
        $notification = Notification::create($title, $body);

        // قم بإنشاء رسالة تستهدف الموضوع (Topic)
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification($notification)
            ->withData($data); // يمكن تمرير بيانات إضافية اختيارياً

        try {
            $this->messaging->send($message);
            \Log::info("Notification sent successfully to topic: {$topic}");
        } catch (\Exception $e) {
            \Log::error("Failed to send notification to topic {$topic}: " . $e->getMessage());
            throw $e; // يمكنك اختيار إعادة رمي الاستثناء أو التعامل معه بشكل مختلف
        }
    }

    // إذا كانت لديك دالة عامة sendNotification، يمكنك إزالتها أو تعديلها لاستدعاء الدالتين الجديدتين.
    // public function sendNotification(string $target, string $title, string $body, string $type = 'token', array $data = [])
    // {
    //     if ($type === 'token') {
    //         return $this->sendNotificationToDevice($target, $title, $body, $data);
    //     } elseif ($type === 'topic') {
    //         return $this->sendNotificationToTopic($target, $title, $body, $data);
    //     }
    //     throw new \InvalidArgumentException('Invalid notification target type.');
    // }
}