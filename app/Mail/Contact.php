<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * お問合せ用メールクラス
 */
class Contact extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * コンストラクタ
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * メール生成
     *
     * @return Contact
     */
    public function build()
    {

        return $this->text('mail.contact')
            ->subject('mail title')
            ->with([
                'title' => $this->data['title'],
            ]);

    }
}
