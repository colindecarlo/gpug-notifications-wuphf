<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class UploadCompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable): array
    {
        return [TwilioChannel::class, 'slack'];
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content("Your upload is complete!");
    }

    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->text("Your upload is complete!");
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
