<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommonJobMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $title;
    public $body;

    /**
     * 新しいメッセージインスタンスを作成
     *
     * @param string $title   メールタイトル
     * @param array $result   置き換え文字
     */
    public function __construct(string $title, array $result)
    {
        $this->title = $title;
        $this->result = $result;
    }

    /**
     * メッセージを作成
     *
     * @see resources/views/emails/Job.blade.php
     * @return JobMail
     */
    public function build(): CommonJobMail
    {
        return $this->from(config('mail.job.address'), config('mail.job.name'))
            ->subject($this->title)
            ->with([
                'result' => $this->result,
            ])
            ->text('emails.common_job');
    }
}
