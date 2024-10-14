<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use DateTime;

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


        $mailTitle = '契約更新のご案内【反社チェックの日本リスク管理センター】';

        // DateTime
        $useStartDate = new DateTime($this->data['useStartDate']);
        $useEndDate = new DateTime($this->data['useEndDate']);

        // 次回利用更新日 = 利用終了日の翌日
        $useUpdateDate = new DateTime($this->data['useEndDate']);
        $useUpdateDate->modify('+1 day');

        return $this->text('mail.userEndAlert')
        ->subject($mailTitle)
        ->with([
            'companyName' => $this->data['companyName'],
            'staffName' => $this->data['staffName'],
            'staffDepartmentJob' => $this->data['staffDepartmentJob'],
            'useStartDate' => $useStartDate,
            'useUpdateDate' => $useUpdateDate,
            'useEndDate' => $useEndDate,
            'idUnitPrice' => $this->data['idUnitPrice'],
            'searchUnitPrice' => $this->data['searchUnitPrice'],
            'chargeName' => $this->data['chargeName'],
            'chargeMail' => $this->data['chargeMail'],
        ]);
    }
}