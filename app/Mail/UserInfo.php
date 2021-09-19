<?php

namespace App\Mail;

use App\Models\MUserCompany;
use App\Models\MUserDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use TCPDF;

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

        $pdfPath = $this->makeReport();

        return $this->text('mail.userInfo')
            ->subject('【JCIS反社チェックDBサービス】ID及びパスワードを発行致しました')
            ->with([
                'companyName' => $this->company['userCompany']['name'],
                'userName' => $this->user['name'],
                'staffName' => $this->company['userCompany']['staffName'],
            ])
            ->attach($pdfPath);


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
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, ['userId' => $this->user['userId'], 'password' => $this->user['password'], $this->company['userCompany']['name']])->render());

        $pdf->Image(resource_path('img/mail-logo.jpg'), 0, 0, 50);

        Storage::makeDirectory(self::TEMP_DIR . $this->user['userId']);

        $pdfPath = storage_path('app/' . self::TEMP_DIR . $this->user['userId']) . '/JCIS反社DBWEB検索アカウント通知書.pdf';
        $pdf->Output($pdfPath, 'F');

        return $pdfPath;
    }


}
