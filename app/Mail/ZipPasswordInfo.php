<?php

namespace App\Mail;

use App\Models\MContractPlan;
use App\Models\MUserCompany;
use App\Models\MUserDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Exception;
use App\Models\TContractPlan;
use Datetime;
use App\Models\BaseModel;

class ZipPasswordInfo extends Mailable
{
    use Queueable, SerializesModels;

    private array $data;
    private array $company;
    private array $user;
    private string $planType;

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
        $planModel = new MContractPlan();

        $this->company = $companyModel->get($this->data['companyId']);
        $this->user = $userModel->get($this->data['companyId'], $this->data['contractPlanId'], $this->data['userId']);

        // web or api 取得
        $planData = $planModel->get($this->data['contractPlanId']);
        $planType = $planData->planType;

        if ($planType === 'web') {

            $this->planType = 'WEB';

        } else if ($planType === 'api') {

            $this->planType = 'API';
        }

        $mailTitle = '【JCIS反社チェックDBサービス】ID及びパスワードを発行致しました';
        $zipName = 'JCIS反社DB'.$this->planType.'検索アカウント通知書.zip';

        // トライアルの場合、メールタイトル・zipファイル名を変更
        if ($planType === 'web' && $this->company['userCompany']['contractStatus'] == BaseModel::STATUS_TRIAL) {
            $mailTitle = '【JCIS反社チェックDBサービス】トライアルID及びパスワードを発行致しました';
            $zipName = 'JCIS反社DB'.$this->planType.'検索トライアルアカウント通知書.zip';
        }

        return $this->text('mail.zipPasswordInfo')
            ->subject('※ パスワード通知：'.$mailTitle)
            ->with([
                'companyName' => $this->company['userCompany']['name'],
                'userName' => $this->user['name'],
                'zipPassword' => $this->data['zipPassword'],
                'zipName' => $zipName,
            ]);
    }
}
