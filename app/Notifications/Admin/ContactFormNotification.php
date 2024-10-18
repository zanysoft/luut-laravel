<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ContactFormNotification extends Notification
{
    use Queueable;

    public $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $app_name = settings('app.name', config('app.name'));

        $name = trim(strip_tags($this->request->first_name) . ' ' . strip_tags($this->request->last_name));

        $attachment = $this->request->file('attachment');

        return (new MailMessage)
            ->subject("Book Now - " . $app_name)
            ->greeting('Dear Concern,')
            ->when($this->request->hasFile('attachment'), function ($msg) {
                $attachment = $this->request->file('attachment');
                $msg->attach($attachment->getRealPath(), [
                    'as' => $attachment->getClientOriginalName(),
                    'mime' => $attachment->getClientMimeType(),
                ]);
            })
            ->line(new HtmlString("<b>Name:</b> " . $name))
            ->line(new HtmlString("<b>Email:</b> " . strip_tags($this->request->email)))
            ->line(new HtmlString("<b>Phone:</b> " . strip_tags($this->request->phone)))
            ->line(strip_tags($this->request->message))
            ->salutation(new HtmlString("Regards,<br>$app_name"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $notifiable->toArray();
    }
}
