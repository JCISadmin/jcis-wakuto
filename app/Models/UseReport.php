<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

/**
 * 利用明細
 */
class UseReport extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = '';

    /**
     * 利用情報を取得
     *
     * @param $companyId
     * @param $userId
     * @return array|null
     */
    public function getList($companyId, $userId): ?array
    {
        $query = DB::table('tContractPlan');

        $query->select(
            'tContractPlan.contractPlanId',
            'tContractPlan.useUpdateDate',
            'tContractPlan.searchUnitPrice',
            'tContractPlan.deposit',
        );

        $query->join('mUserDetail', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'mUserDetail.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'mUserDetail.contractPlanId');
        });

        $query->where('mUserDetail.companyId', $companyId);
        $query->where('mUserDetail.userId', $userId);

        $data = $query->first();

        $model = new TKeywordHistory();
        $monthSearchCount = $model->getMonthSearchCount($companyId, $userId, $data);
        $yearSearchCount = $model->getYearSearchCount($companyId, $userId, $data);
        $depositBalance = $data->deposit - $data->searchUnitPrice * $yearSearchCount;

        $list = [
            'monthSearchCount' => $monthSearchCount,
            'yearSearchCount' => $yearSearchCount,
            'depositBalance' => $depositBalance,

        ];

        return $list;
    }

    /**
     * 利用情報を取得
     *
     * @param $companyId
     * @param $userId
     * @return object
     */
    public function makePdf($companyId, $userId)
    {
        $dataAry = $this->getList($companyId, $userId);
        $dt = new Datetime();
        $date = $dt->format('Y年n月j日');
        $dataAry['userId'] = $userId;
        $dataAry['printDate'] = $date;

        //PDF生成
        $pdfTemplate = 'pdf.pdfUseReport';
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $dataAry)->render());

        return $pdf;
    }


}
