<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use TCPDF;
use App\Models\MContractPlan;

/**
 * 利用明細
 */
class UseReport extends Report
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

        $mContractPlan = new MContractPlan();
        $type = $mContractPlan->getPlanType($data->contractPlanId);
        $yearSearchCount = $model->getSearchCount($companyId, $type, $userId, $startDate, $endDate);
        $depositBalance = $data->deposit;

        $dt = new Datetime();
        $date = $dt->format('Y年n月j日H時i分');

        return [
            'date' => $date,
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
    public function makePdf($fileName, $companyId, $dispType, $month, $pageNo): string
    {
        //現在日時
        $now = new Datetime();
        $date = $now->format('Y年n月j日H時i分');

        //会社名
        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);

        //表示データ取得
        $model = new Report();

        //全件指定
        if($dispType === 'all'){

            $pageInfo = $model->getReportPageInfo($companyId, $pageNo, 'user');
            $pageData = $pageInfo['pageData'];
            $pageAry = $pageData->items();
            $pageItem = array_values($pageAry);
            $year = $pageItem[0];
    
            $data = $model->getReportData($companyId, $year);

            $detail = [
                'month' => $data['month'],
                'year' => $data['year'],
            ];

        //月別指定
        }elseif($dispType === 'month'){

            $useMonth = new DateTime($month);
            //指定月が現在より先
            if($now < $useMonth){
                $detail = null;
            }else{
                $useY = $useMonth->format('Y');
                $useYM = $useMonth->format('Y-m');

                $data = $model->getReportData($companyId, $useY);

                if(!isset($data['month'][$useY][$useYM])){
                    $detail = null;
                }else{

                    $detail['month'][$useY][$useYM] = $data['month'][$useY][$useYM];
                    $detail['year'][$useY] = $data['year'][$useY];
                }
            }
        }

        //今月検索件数/年間検索件数/デポジット検索欄
        $monthSearchCount = 0;
        $yearSearchCount = 0;
        $nowData = $model->getReportData($companyId, $now->format('Y'));

        if(isset($nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'])){
            $monthSearchCount = $nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'];
        }
        if(isset($nowData['year'][$now->format('Y')]['totalSearchCount'])){
            $yearSearchCount = $nowData['year'][$now->format('Y')]['totalSearchCount'];
        }

        $pdfData = [
            'date' => $date,
            'companyName' => $companyName,
            'monthSearchCount' => $monthSearchCount,
            'yearSearchCount' => $yearSearchCount,
            'detail' => $detail,
            'dispType' => $dispType,
        ];

        //PDF生成
        $pdfTemplate = 'pdf.pdfUseReport';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

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
