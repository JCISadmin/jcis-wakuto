<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\UseReport;

/**
 * 利用明細画面
 */
class UseReportController extends Controller
{

    /**
     * 初期画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new UseReport();
        $userId = auth()->user()->userId;
        $companyId = auth()->user()->companyId;


        $assignAry = [
            'useReportList' => $model->getList($companyId, $userId),
        ];

        return view('user/useReport/list', $assignAry);
    }

    /**
    * 利用明細
    *
    * @param Request $request
    * @return string
    */
   public function printUseReport(Request $request) {

        $model = new UseReport();
        $userId = auth()->user()->userId;
        $companyId = auth()->user()->companyId;
        $pdf = $model->makePdf($companyId, $userId);

        //PDFファイル名(利用明細-[ymd].pdf)
        $pdfName = '利用明細-%s.pdf';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        $fileName = mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');

        $string = $pdf->Output( $fileName, "S" );

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");

        return $string;
   } 


}
