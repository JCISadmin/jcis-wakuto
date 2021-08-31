<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * お問合せ用メールクラス
 */
class Contact extends Mailable
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
     * @return Contact
     */
    public function build(): Contact
    {

        return $this->text('mail.contact')
            ->subject(config('hds.contact.mailSubject'))
            ->with([
                'subject' => $this->data['subject'],
                'contactDetail' => $this->data['contactDetail'],
                'name' => $this->data['name'],
                'mail' => $this->data['mail'],
                'companyId' => $this->data['companyId'],
                'departmentJob' => $this->data['departmentJob'],
            ]);

    }
}
