<?php

namespace App\Mail;

use App\Models\MUserCompany;
use App\Models\MUserDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Exception;

class ZipPasswordInfo extends Mailable
{
    use Queueable, SerializesModels;

    private array $data;
    private array $company;
    private array $user;

    /**
     * コンストラクタ
     *
     * @param $zipPassword
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * メール生成
     *
     * @return ZipPasswordInfo
     */
    public function build(): ZipPasswordInfo {

        $companyModel = new MUserCompany();
        $userModel = new MUserDetail();

        $this->company = $companyModel->get($this->data['companyId']);
        $this->user = $userModel->get($this->data['companyId'], $this->data['contractPlanId'], $this->data['userId']);

        $mailTitle = '【JCIS反社チェックDBサービス】ID及びパスワードを発行致しました';

        // トライアルの場合、メールタイトルを変更
        $trialPlan = config('hds.'.'contract')['trialPlan']['web'];
        if ($this->data['contractPlanId'] === $trialPlan) {
            $mailTitle = '【JCIS反社チェックDBサービス】トライアルID及びパスワードを発行致しました';
        }

        return $this->text('mail.zipPasswordInfo')
            ->subject('※ パスワード通知：'.$mailTitle)
            ->with([
                'companyName' => $this->company['userCompany']['name'],
                'userName' => $this->user['name'],
                'zipPassword' => $this->data['zipPassword'],
                'zipName' => 'JCIS反社DBWEB検索アカウント通知書.zip',
            ]);
    }
}
