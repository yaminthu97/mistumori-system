<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommonMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $title;
    public $body;

    /**
     * 新しいメッセージインスタンスを作成
     *
     * @param string $title メールタイトル
     * @param string $body  メール本文
     */
    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * メッセージを作成
     *
     * @see resources/views/emails/template.blade.php
     * @return CommonMail
     */
    public function build(): CommonMail
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->title)
            ->with([
                'body' => $this->body,
            ])
            ->text('emails.template');
    }
}
