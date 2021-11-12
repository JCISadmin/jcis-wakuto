<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Mail;

use App\Models\BaseModel;
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
    private array $trialDate = [];

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

        $mailTitle = '【JCIS反社チェックDBサービス】ID及びパスワードを発行致しました';
        $zipName = 'JCIS反社DB'.$this->planType.'検索アカウント通知書.zip';

        // トライアルの場合のメール表記変更
        if ($planType === 'web' && $this->company['userCompany']['contractStatus'] == BaseModel::STATUS_TRIAL) {
            $mailTitle = '【JCIS反社チェックDBサービス】トライアルID及びパスワードを発行致しました';
            $zipName = 'JCIS反社DB'.$this->planType.'検索トライアルアカウント通知書.zip';
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

            $this->trialDate = $trialDate;
        }

        $pdfPath = $this->makeReport();
        $zipPath = $this->makeZip($pdfPath);
        unlink($pdfPath);

        return $this->text($mailText)
            ->subject($mailTitle)
            ->with([
                'companyName' => $this->company['userCompany']['name'],
                'userName' => $this->user['name'],
                'staffName' => $this->company['userCompany']['staffName'],
                'trialDate' => $this->trialDate,
            ])
            ->attach($zipPath, [
                'as' => $zipName,
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

        $pdf->SetFont('kozminproregular','',10);
        $pdf->Text(20, 40, $this->company['userCompany']['name']." 御中");

        $pdf->Text(135, 60, "日本信用情報サービス株式会社");

        $pdf->SetFont('kozminproregular','B',10);

        if($this->planType === 'WEB'){
            // サブタイトル
            $pdf->Text(20, 110, "★JCIS反社DBWEB検索ページ");

            //　URL
            $pdf->SetFont('kozminproregular','',9);
            $pdf->Text(20, 130, "検索ページのURL：");
            $pdf->Text(20, 140, config('hds.url.web'));

            if($this->company['userCompany']['contractStatus'] == BaseModel::STATUS_TRIAL){
                //【トライアル】
                $pdfName = 

                // タイトル
                $pdf->SetFont('kozminproregular','',16);
                $pdf->Text(30, 80, "JCIS 反社WEBDB - 接続用 トライアルIDパスワード通知書");
                
                // トライアル期間
                $pdf->SetFont('kozminproregular','',9);
                $pdf->Text(20, 160, "トライアル期間：".$this->trialDate['startTrial']."〜".$this->trialDate['endTrial']."（14日間）");
                $pdf->Text(20, 170, "終了2日前（".$this->trialDate['noticeEndTrial']."）までに本契約移行の可否のご連絡を必ずお願い致します。");

                $startY = 170;

            }else{
                //【契約中】

                // タイトル
                $pdf->SetFont('kozminproregular','',16);
                $pdf->Text(50, 80, "JCIS 反社WEBDB - 接続用 IDパスワード通知書");

                $pdf->SetFont('kozminproregular','',9);

                $startY = 140;
            }

        }elseif($this->planType === 'API'){            
            // サブタイトル
            $pdf->Text(20, 110, "★JCIS反社DB API検索");

            // タイトル
            $pdf->SetFont('kozminproregular','',16);
            $pdf->Text(50, 80, "JCIS 反社APIDB - 接続用 IDパスワード通知書");

            //　URL
            $pdf->SetFont('kozminproregular','',9);
            $pdf->Text(20, 130, "エンドポイントURL：");
            $pdf->Text(20, 140, "【反社DB検索 API】".config('hds.url.api.search'));
            $pdf->Text(20, 150, "【利用状況確認 API】".config('hds.url.api.useReport'));
            $pdf->Text(20, 160, "※仕様の詳細は提供しておりますAPI仕様書をご覧ください。");
            $startY = 160;

        }

        $pdf->Text(20, $startY + 20, "ユーザーID：".$this->user['userId']);
        $pdf->Text(20, $startY + 30, "パスワード：".$this->user['password']);

        $pdf->SetFont('kozminproregular','B',10);
        $pdf->Text(20, $startY + 70, "※検索用のアカウントとパスワードは絶対に外部に公開しないでください");

        $pdf->SetFont('kozminproregular','',9);
        $nowDate = date("Y年m月d日");
        $pdf->Text(20, $startY + 100, "※".$nowDate."現在の情報です");
        $pdf->Image(resource_path('img/mail-logo.jpg'), 125, 10, 80);

        Storage::makeDirectory(self::TEMP_DIR . $this->user['userId']);

        // PDFファイル名をSJISとして保存
        if ($this->planType === 'WEB' && $this->company['userCompany']['contractStatus'] == BaseModel::STATUS_TRIAL) {
            //【WEB・トライアル】
            $fileName = mb_convert_encoding('/JCIS反社DB'.$this->planType.'検索トライアルアカウント通知書.pdf', 'sjis-win', 'UTF-8');        
        } else{
            $fileName = mb_convert_encoding('/JCIS反社DB'.$this->planType.'検索アカウント通知書.pdf', 'sjis-win', 'UTF-8');
        }
        $pdfPath = storage_path('app/' . self::TEMP_DIR . $this->user['userId']) . $fileName;
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

        $zipFileName = storage_path('app/' . self::TEMP_DIR . $this->user['userId']) . 'JCIS反社DB'.$this->planType.'検索アカウント通知書.zip';
        $password = $this->data['zipPassword'];

        // zipファイル生成前に強制削除
        @unlink($zipFileName);

        $execParam = 'zip -P %s -j %s';
        $execStr = sprintf($execParam, $password, $zipFileName) . ' ' . $pdfPath;

        //トライアルの場合、約款を含める
        if ($this->planType === 'WEB' && $this->company['userCompany']['contractStatus'] == BaseModel::STATUS_TRIAL) {
            // 約款PDFのファイル名をSJISに変更
            $termPathOrg = resource_path(self::TERMS_DIR);
            $termPath = storage_path(mb_convert_encoding('情報提供業務利用約款.pdf', 'sjis-win', 'UTF-8'));
            copy($termPathOrg, $termPath);

            $execStr .= ' ' . $termPath;
        }

        system($execStr . ' > /dev/null 2>&1');

        return $zipFileName;
    }

}
