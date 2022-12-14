<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JoinDiscussion extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
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
            ->subject('Discussion Request')
            ->from($this->details['from_email'], $this->details['from_name'])
            ->greeting($this->details['greeting'])
            ->line($this->details['body'])
            ->action($this->details['actionText'], $this->details['url']);
    }

    public function toDatabase($notifiable)
    {
        return [
            'notification' => $this->details['body'],
            'id' => $this->details['id'],
            'sender_id' => $this->details['sender_id'],
            'sender' => $this->details['sender'],
            'type' => 'discussion request',
            'url' => $this->details['url']

        ];
    }
}
