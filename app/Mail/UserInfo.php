<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Mail;

use App\Models\MContractPlan;
use App\Models\MUserCompany;
use App\Models\MUserDetail;
use App\Models\TContractPlan;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use TCPDF;
use Datetime;
use ZipArchive;

class UserInfo extends Mailable
{
    use Queueable, SerializesModels;

    private array $data;
    private array $company;
    private array $user;
    private string $planType;

    const TEMP_DIR = 'mailTemp/';
    const TERMS_DIR = 'pdf/情報提供業務利用約款.pdf';

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
     * @return UserInfo
     * @throws Exception
     */
    public function build(): UserInfo
    {

        $companyModel = new MUserCompany();
        $userModel = new MUserDetail();
        $planModel = new MContractPlan();

        $this->company = $companyModel->get($this->data['companyId']);
        $this->user = $userModel->get($this->data['companyId'], $this->data['contractPlanId'], $this->data['userId']);

        // web or api 取得
        $planData = $planModel->get($this->data['contractPlanId']);
        $planType = $planData->planType;

        if ($planType === 'web') {

            $mailText = 'mail.userInfoWeb';
            $this->planType = 'WEB';

            $contractModel = new TContractPlan();
            $contractData = $contractModel->getPlan($this->data['companyId'], 'web');

        } else if ($planType === 'api') {

            $mailText = 'mail.userInfoApi';
            $this->planType = 'API';
        }

        $pdfPath = $this->makeReport();
        $zipPath = $this->makeZip($pdfPath);
        unlink($pdfPath);

        $mailTitle = '【JCIS反社チェックDBサービス】ID及びパスワードを発行致しました';
        $trialDate = array();

        // トライアルの場合のメール表記変更
        if ($planType === 'web') {
            $dt = new Datetime();
            $today = date_format($dt, 'Y-m-d');
            $dtUseStart = $contractData['useStartDate'];

            if (is_null($dtUseStart)){
                //利用開始日が未登録
                $isTrial = true;
            }elseif($today < $dtUseStart){
                //メール送信時の日付が利用開始日より前
                $isTrial = true;
            }else{
                $isTrial = false;
            }

            if ($isTrial){
                $mailTitle = '【JCIS反社チェックDBサービス】トライアルID及びパスワードを発行致しました';
                $mailText = 'mail.trialInfo';

                $trialDate = new DateTime($contractData['startTrial']);
                $startTrial = $trialDate->format('Y年m月d日');
                $trialDate->modify('+14 days');
                $endTrial = $trialDate->format('Y年m月d日');
                $trialDate->modify('-2 days');
                $noticeEndTrial = $trialDate->format('m月d日');

                $trialDate = [
                    'startTrial' => $startTrial,
                    'endTrial' => $endTrial,
                    'noticeEndTrial' => $noticeEndTrial,
                ];
            }
        }

        return $this->text($mailText)
            ->subject($mailTitle)
            ->with([
                'companyName' => $this->company['userCompany']['name'],
                'userName' => $this->user['name'],
                'staffName' => $this->company['userCompany']['staffName'],
                'trialDate' => $trialDate,
            ])
            ->attach($zipPath, [
                'as' => 'JCIS反社DB'.$this->planType.'検索アカウント通知書.zip',
            ]);


    }

    /**
     * 通知書PDF生成
     * @return string
     */
    private function makeReport(): string
    {
        //PDF生成
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();

        $pdf->SetFont('kozminproregular','',16);
        $pdf->Text(50, 80, "JCIS 反社".$this->planType."DB - 接続用 IDパスワード通知書");

        $pdf->SetFont('kozminproregular','',10);
        $pdf->Text(135, 60, "日本信用情報サービス株式会社");

        $pdf->SetFont('kozminproregular','B',10);
        $pdf->Text(20, 110, "★JCIS反社DB".$this->planType."検索ページ");
        $pdf->Text(20, 190, "※検索用のアカウントとパスワードは絶対に外部に公開しないでください");

        $pdf->SetFont('kozminproregular','',9);
        $pdf->Text(20, 130, "検索ページのURL：");
        $pdf->Text(20, 140, "https://jcisdb.com/hansha/");
        $pdf->Text(20, 160, "ユーザーID：".$this->user['userId']);
        $pdf->Text(20, 170, "パスワード：".$this->user['password']);

        $nowDate = date("Y年m月d日");
        $pdf->Text(20, 245, "※".$nowDate."現在の情報です");
        $pdf->Image(resource_path('img/mail-logo.jpg'), 125, 10, 80);

        Storage::makeDirectory(self::TEMP_DIR . $this->user['userId']);

        $pdfPath = storage_path('app/' . self::TEMP_DIR . $this->user['userId']) . '/JCIS反社DB'.$this->planType.'検索アカウント通知書.pdf';
        $pdf->Output($pdfPath, 'F');

        return $pdfPath;
    }

    /**
     * パスワード付きzipファイル作成
     *
     * @param $pdfPath
     * @return string $zipPath
     */
    private function makeZip($pdfPath): string
    {
        $zip = new ZipArchive();

        $zipFileName = storage_path('app/' . self::TEMP_DIR . $this->user['userId']) . 'JCIS反社DB'.$this->planType.'検索アカウント通知書.zip';
        $password = $this->data['zipPassword'];
        $termPath = resource_path(self::TERMS_DIR);

        $zip->open($zipFileName, ZipArchive::CREATE|ZipArchive::OVERWRITE);
        $zip->setPassword($password);
        $zip->addFile($pdfPath, 'JCIS反社DB'.$this->planType.'検索アカウント通知書.pdf');
        $zip->addFile($termPath, '情報提供業務利用約款.pdf');
        $zip->setEncryptionName('JCIS反社DB'.$this->planType.'検索アカウント通知書.pdf', ZipArchive::EM_TRAD_PKWARE);
        $zip->setEncryptionName('情報提供業務利用約款.pdf', ZipArchive::EM_TRAD_PKWARE);
        $zip->close();

        return $zipFileName;
    }

}
