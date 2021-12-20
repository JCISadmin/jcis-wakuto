<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * ユーザー利用終了通知メールクラス
 */
class UserEndAlert extends Mailable
{
    use Queueable, SerializesModels;

    private array $data;

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
     * @return UserEndAlert
     */
    public function build(): UserEndAlert {

        return $this->text('mail.userEndAlert')
        ->subject( 'test' )
        ->with([

        ]);
    }
}