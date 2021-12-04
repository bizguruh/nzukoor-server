<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Messages\MailMessage;

class EventReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $details;
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {

        Log::info('*** toWebPush ***');
        Log::info('*** new message: ' . $this->details['title']);

        return (new WebPushMessage)
            ->title($this->details['title'])
            ->body($this->details['message'])
            ->action('reply', $this->details['url'])
            ->image($this->details['image']);
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Event Reminder')
            ->from($this->details['from_email'], $this->details['from_name'])
            ->greeting($this->details['greeting'])
            ->line($this->details['body'])
            ->action($this->details['actionText'], $this->details['url']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'notification' => $this->details['body'],

        ];
    }
}
