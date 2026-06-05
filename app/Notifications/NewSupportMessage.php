<?php

namespace App\Notifications;

use App\Models\SupportMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSupportMessage extends Notification
{
    use Queueable;

    public function __construct(
        public SupportMessage $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $type = ucfirst($this->message->type);

        return [
            'message' => "New {$type}: \"{$this->message->subject}\" from {$this->message->name} ({$this->message->email}).",
            'url' => route('admin.support.messages.show', $this->message->id),
        ];
    }
}
