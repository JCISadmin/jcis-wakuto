<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\UseReport;
use Illuminate\Http\RedirectResponse;
use DateTime;

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

        $detail = $model->getReportData($companyId);

        $assignAry = [
            'useReportList' => $model->getList($companyId, $userId),
            'detail' => $detail,
            'useMonth' => '',
            'dispType' => 'all',
        ];

        return view('user/useReport/list', $assignAry);
    }

    /**
    * 利用明細PDF
    *
    * @param Request $request
    * @return string
    */
   public function printUseReport(Request $request): string
   {

        $model = new UseReport();
        $userId = auth()->user()->userId;
        $companyId = auth()->user()->companyId;
        $fileName = $model->getFileName();
        $string = $model->makePdf($companyId, $userId, $fileName);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");

        return $string;
   }


}
