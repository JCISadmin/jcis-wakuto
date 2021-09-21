<?php

namespace App\Mail;

use App\Models\BaseModel;
use App\Models\MUserCompany;
use App\Models\MUserDetail;
use App\Models\TContractPlan;
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

    const TEMP_DIR = 'mailTemp/';

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
     */
    public function build(): UserInfo
    {

        $companyModel = new MUserCompany();
        $userModel = new MUserDetail();

        $this->company = $companyModel->get($this->data['companyId']);
        $this->user = $userModel->get($this->data['companyId'], $this->data['contractPlanId'], $this->data['userId']);

        // zipファイル解答のためのパスワード生成
        $baseModel = new BaseModel();
        $zipPassword = $baseModel->makePassword();

        $pdfPath = $this->makeReport();
        $zipPath = $this->makeZip($pdfPath, $zipPassword);

        $mailTitle = '【JCIS反社チェックDBサービス】ID及びパスワードを発行致しました';
        $mailText = 'mail.userInfo';
        $trialDate = array();

        // トライアルの場合のメール表記変更
        $trialPlan = config('hds.'.'contract')['trialPlan'];
        foreach($trialPlan as $key => $item) {
            if ($this->data['contractPlanId'] === $item) {
                $mailTitle = '【JCIS反社チェックDBサービス】トライアルID及びパスワードを発行致しました';
                $mailText = 'mail.trialInfo';
    
                $contractModel = new TContractPlan();
                $contractData = $contractModel->getPlan($this->data['companyId'], $key);
    
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
            ->attach($zipPath);


    }

    /**
     * 通知書PDF生成
     * @return string
     */
    private function makeReport(): string
    {
        //PDF生成
        $pdfTemplate = 'pdf.userInfo';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        
        $pdf->SetFont('kozminproregular','',16);
        $pdf->Text(50, 80, "JCIS 反社WEBDB - 接続用 IDパスワード通知書");

        $pdf->SetFont('kozminproregular','',10);
        $pdf->Text(135, 60, "日本信用情報サービス株式会社");

        $pdf->SetFont('kozminproregular','B',10);
        $pdf->Text(20, 110, "★JCIS反社DBWEB検索ページ");
        $pdf->Text(20, 190, "※検索用のアカウントとパスワードは絶対に外部に公開しないでください");

        $pdf->SetFont('kozminproregular','',9);
        $pdf->Text(20, 130, "検索ページのURL：");
        $pdf->Text(20, 140, "https://jcisdb.com/hansha/");
        $pdf->Text(20, 160, "ユーザーID：".$this->user['userId']);
        $pdf->Text(20, 170, "パスワード：".$this->user['password']);
        // TODO 日付を変数に変更する
        $pdf->Text(20, 245, "※202X年XX月XX日現在の情報です");
        $pdf->Image(resource_path('img/mail-logo.jpg'), 125, 10, 80);

        Storage::makeDirectory(self::TEMP_DIR . $this->user['userId']);

        $pdfPath = storage_path('app/' . self::TEMP_DIR . $this->user['userId']) . '/JCIS反社DBWEB検索アカウント通知書.pdf';
        $pdf->Output($pdfPath, 'F');

        return $pdfPath;
    }

    /**
     * パスワード付きzipファイル作成
     * 
     * @param string $pdfPath
     * @param string $password
     * @return string $zipPath
     */
    private function makeZip($pdfPath, $password) {
        $zip = new ZipArchive();

        $zipFileName = storage_path('app/' . self::TEMP_DIR . $this->user['userId']) . 'JCIS反社DBWEB検索アカウント通知書.zip';

        $zip->open($zipFileName, ZipArchive::CREATE|ZipArchive::OVERWRITE);
        $zip->setPassword($password);
        $zip->addFile($pdfPath, '/JCIS反社DBWEB検索アカウント通知書.pdf');
        $zip->close();

        unlink($pdfPath);

        return $zipFileName;
    }

}
