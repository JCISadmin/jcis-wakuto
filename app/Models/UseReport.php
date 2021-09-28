<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use TCPDF;

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
     * @throws Exception
     */
    public function getList($companyId, $userId): ?array
    {
        $query = DB::table('tContractPlan');

        $query->select(
            'tContractPlan.contractPlanId',
            'tContractPlan.useStartDate',
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

        /** @var object $data */
        $data = $query->first();

        $model = new TKeywordHistory();

        //今月検索数
        $today = new Datetime();
        $year = $today->format('Y');
        $month = $today->format('m');
        $monthSearchCount = $model->getMonthSearchCount($companyId, $userId, $data->contractPlanId, $year, $month);

        //年間検索数
        if (is_null($data->useUpdateDate)) {
            //契約更新日が未登録の場合、契約開始日を基準日とする
            $baseDate = new DateTime($data->useStartDate);
        } else {
            //契約更新日が登録済の場合、契約更新日を基準日とする
            $baseDate = new DateTime($data->useUpdateDate);
        }

        $dtStart = clone $baseDate;
        $dtEnd = clone $baseDate;
        if ($today < $baseDate) {
            //開始日：1年前の基準日
            $startDate = date_format($dtStart->modify('-01 year'), 'Y-m-d 0:00:00');
            //終了日：基準日の1日前
            $endDate = date_format($dtEnd->modify('-01 day'), 'Y-m-d 23:59:59');
        } else {
            //開始日：基準日
            $startDate = date_format($dtStart, 'Y-m-d 0:00:00');
            //終了日：1年後の基準日の1日前
            $endDate = date_format($dtEnd->modify('+01 year -01 day'), 'Y-m-d 23:59:59');
        }

        $yearSearchCount = $model->getSearchCount($companyId, $data->contractPlanId, $userId, $startDate, $endDate);
        $depositBalance = $data->deposit - $data->searchUnitPrice * $yearSearchCount;

        return [
            'monthSearchCount' => $monthSearchCount,
            'yearSearchCount' => $yearSearchCount,
            'depositBalance' => $depositBalance,

        ];

    }

    /**
     * 利用情報を取得
     *
     * @param $companyId
     * @param $userId
     * @param $fileName
     * @return string
     * @throws Exception
     */
    public function makePdf($companyId, $userId ,$fileName): string
    {
        $dataAry = $this->getList($companyId, $userId);
        $dt = new Datetime();
        $date = $dt->format('Y年n月j日');
        $dataAry['userId'] = $userId;
        $dataAry['printDate'] = $date;

        //PDF生成
        $pdfTemplate = 'pdf.pdfUseReport';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $dataAry)->render());

        return  $pdf->Output( $fileName, "S" );
    }

    /**
     * ファイル名を取得
     *
     * @return string
     */
    public function getFileName(): string
    {
        $pdfName = '利用明細-%s.pdf';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');

    }

}
