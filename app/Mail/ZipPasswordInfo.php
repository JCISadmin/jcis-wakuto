<?php

namespace App\Mail;

use App\Models\BaseModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ZipPasswordInfo extends Mailable
{
    use Queueable, SerializesModels;

    private string $zipPassword;

    /**
     * コンストラクタ
     *
     * @param $data
     */
    public function __construct($zipPassword) {
        $this->zipPassword = $zipPassword;
    }

    /**
     * メール生成
     * 
     * @return ZipPasswordInfo
     */
    public function build(): ZipPasswordInfo {

        return $this->text('mail.zipPasswordInfo')
            ->subject('【JCIS反社チェックDBサービス】zipファイルパスワードを発行致しました')
            ->with([
                'zipPassword' => $this->zipPassword,
            ]);
    }
}