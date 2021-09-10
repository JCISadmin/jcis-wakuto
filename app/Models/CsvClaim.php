<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * 請求書
 */
class CsvClaim extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    const EXPORT_DIR = 'storage/app/csvClaim';

    private $HEADER = array(
        '会社ID',
        '会社名',
        '請求番号',
        '請求日',
        '支払期日',
        '請求金額',
        '郵便番号',
        '会社住所',
        '代表電話番号',
        '請求者名',
        '請求者部署・役職',
        '請求電話番号',
        '契約プラン',
        '契約形態',
        'ID個数',
        'ID代',
        '検索単価',
        '月間検索数',
        'デポジット残額',
    );

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     * 
     * @param $claimMonth
     * @param $ids
     */
    public function makeCsv($claimMonth, $ids) {

        $data = $this->getData($claimMonth, $ids);
        $fileName = tmpfile();
        $filePath = storage_path(self::EXPORT_DIR) . '/' . $fileName;
        $fp = fopen($filePath, 'w');
        fputcsv($fp, $this->HEADER);

        foreach ($data as $item) {

            $claimDate = $item->claimDate === null ? '' : date_format(new Datetime($item->claimDate), 'Y/m/d');
            $paymentDate = $item->paymentDate === null ? '' : date_format(new Datetime($item->paymentDate), 'Y/m/d');
            
            $webPlanIds = $item->webPlanIds === null ? 0 : $item->webPlanIds;
            $webPlanIdUnitPrice = $item->webPlanIdUnitPrice === null ? 0 : $item->webPlanIdUnitPrice;
            $webPlanSearchCount = $item->webPlanSearchCount === null ? 0 : $item->webPlanSearchCount;
            $webPlanDeposit = $item->webPlanDeposit === null ? 0 : $item->webPlanDeposit;
            $webDepositBalance = $webPlanDeposit - $webPlanIds * $webPlanSearchCount;

            $apiPlanIds = $item->apiPlanIds === null ? 0 : $item->apiPlanIds;
            $apiPlanIdUnitPrice = $item->apiPlanIdUnitPrice === null ? 0 : $item->apiPlanIdUnitPrice;
            $apiPlanSearchCount = $item->apiPlanSearchCount === null ? 0 : $item->apiPlanSearchCount;
            $apiPlanDeposit = $item->apiPlanDeposit === null ? 0 : $item->apiPlanDeposit;
            $apiDepositBalance = $apiPlanDeposit - $apiPlanIds * $apiPlanSearchCount;

            $row = [
                $item->companyId,
                $item->companyName,
                $item->claimNo,
                $claimDate,
                $paymentDate,
                $item->price,
                $item->postCode,
                $item->address,
                $item->tel,
                $item->claimName,
                $item->claimDepartmentJob,
            ];

            $webRow = [
                $row,
                $item->webPlanPlanName,
                $item->webPlanTypeName,
                $webPlanIds,
                $webPlanIdUnitPrice,
                $webPlanSearchUnitPrice,
                $webPlanSearchCount,
                $webDepositBalance  
            ];

            $apiRow = [
                $item->apiPlanPlanName,
                $item->apiPlanTypeName,
                $apiPlanIds,
                $apiPlanIdUnitPrice,
                $apiPlanSearchUnitPrice,
                $apiPlanSearchCount,
                $apiDepositBalance    
            ];

            fputcsv($fp, $webRow);
            fputcsv($fp, $apiRow);
        }
        fclose($fp);
    }

    /**
     * 
     * @param $claimMonth
     * @param $ids
     */
    public function getData($claimMonth, $ids) {

        $ary = explode('-', $claimMonth);

        $claimY = $ary[0];
        $claimM = $ary[1];

        $idAry = [];
        foreach($ids['exportFlg'] as $kay => $id){
            $idAry[] = $id; 
        }
    
        $idNum = DB::table('mUserDetail');
        $idNum->select(
            'companyId',
            'contractPlanId',
            DB::raw('count(*) as ids')
        );
        $idNum->where('delFlg', self::DEL_FLG_OFF);
        $idNum->groupBy(['companyId', 'contractPlanId']);

        $search = DB::table('tKeywordHistory');
        $search->select(
            'companyId',
            'contractPlanId',
            'searchDate',
            DB::raw('count(*) as searchCount')
        );
        $search->whereYear('searchDate', $claimY);
        $search->whereMonth('searchDate', $claimM);
        $search->groupBy(['companyId', 'contractPlanId', 'searchDate']);

        $webPlan = DB::table('tContractPlan');
        $webPlan->select(
            'tContractPlan.companyId',
            'tContractPlan.contractPlanId',
            'tContractPlan.contractTypeId',
            'tContractPlan.idUnitPrice',
            'tContractPlan.searchUnitPrice',
            'tContractPlan.deposit',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'mContractType.name as contractTypeName',
            'webPlanIds.ids',
            'webSearch.searchCount',
        );
        $webPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->join('mContractType', function ($join) {
            $join->on('tContractPlan.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $webPlan->joinSub($idNum, 'webPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->joinSub($search, 'webSearch', function($join) {
            $join->on('tContractPlan.companyId', '=', 'webSearch.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webSearch.contractPlanId');
        });
        $webPlan->where('mContractPlan.planType', 'web');

        $apiPlan = DB::table('tContractPlan');
        $apiPlan->select(
            'tContractPlan.companyId',
            'tContractPlan.contractPlanId',
            'tContractPlan.contractTypeId',
            'tContractPlan.idUnitPrice',
            'tContractPlan.searchUnitPrice',
            'tContractPlan.deposit',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'mContractType.name as contractTypeName',
            'apiPlanIds.ids',
            'apiSearch.searchCount',
        );
        $apiPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->join('mContractType', function ($join) {
            $join->on('tContractPlan.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $apiPlan->joinSub($idNum, 'apiPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });

        $apiPlan->joinSub($search, 'apiSearch', function($join){
            $join->on('tContractPlan.companyId', '=', 'apiSearch.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiSearch.contractPlanId');
        });

        $apiPlan->where('mContractPlan.planType', 'api');

        $claim = DB::table('tClaim');
        $claim->select(
            'tClaim.*',
        );
        $claim->where('claimMonth', $claimY.$claimM);
        
        $query = DB::table('mUserCompany');
        $query->select(
            'mUserCompany.companyId',
            'mUserCompany.name as companyName',
            'claim.claimNo',
            'claim.claimDate',
            'claim.paymentDate',
            'claim.price',
            'mUserCompany.postCode',
            'mUserCompany.address',
            'mUserCompany.tel',
            'mUserCompany.claimName',
            'mUserCompany.claimDepartmentJob',
            'webPlan.companyId as webPlanCompanyId',
            'webPlan.contractPlanName as webPlanPlanName',
            'webPlan.contractTypeName as webPlanTypeName',
            'webPlan.ids as webPlanIds',
            'webPlan.searchCount as webPlanSearchCount',
            'webPlan.deposit as webPlanDeposit',
            'webPlan.idUnitPrice as webPlanIdUnitPrice',
            'webPlan.searchUnitPrice as webPlanSearchUnitPrice',
            'apiPlan.companyId as apiPlanCompanyId',
            'apiPlan.contractPlanName as apiPlanPlanName',
            'apiPlan.contractTypeName as apiPlanTypeName',
            'apiPlan.idUnitPrice as apiPlanIdUnitPrice',
            'apiPlan.searchUnitPrice as apiPlanSearchUnitPrice',
            'apiPlan.ids as apiPlanIds',
            'apiPlan.searchCount as apiPlanSearchCount',
            'apiPlan.deposit as apiPlanDeposit',           
        );
        $query->leftJoinSub($webPlan, 'webPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'webPlan.companyId');
        });
        $query->leftJoinSub($apiPlan, 'apiPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'apiPlan.companyId');
        });
        $query->leftJoinSub($idNum, 'userDetail', function($join){
            $join->on('mUserCompany.companyId', '=', 'userDetail.companyId');
        });
        $query->leftJoinSub($search, 'keywordHistory', function($join){
            $join->on('mUserCompany.companyId', '=', 'keywordHistory.companyId');
        });
        $query->leftJoinSub($claim, 'claim', function($join){
            $join->on('mUserCompany.companyId', '=', 'claim.companyId');
        });

        $query->whereIn('mUserCompany.companyId', $idAry);
        $data = $query->get();

        return $data;


    }

}