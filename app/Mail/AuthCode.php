<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * 認証コードメールクラス
 */
class AuthCode extends Mailable
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
     * メール作成
     *
     * @return AuthCode
     */
    public function build(): AuthCode
    {

        return $this->text('mail.authCode')
            ->subject(config('hds.auth.mailSubject'))
            ->with([
                'authCode' => $this->data['authCode'],
            ]);

    }

}
