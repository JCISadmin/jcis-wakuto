<?php

namespace App\Models;

use App\Models\TClaim;
use DateTime;

/**
 * 請求書
 */
class CsvClaim extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

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

    const CSV_CLAIM_PATH = 'app/csvClaim';

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     * 
     * @param $claimMonth
     * @param $ids
     * @return $csvInfo
     */
    public function makeCsv($claimMonth, $ids) {

        if(file_exists(storage_path(self::CSV_CLAIM_PATH)) === false){
            mkdir(storage_path(self::CSV_CLAIM_PATH), 0777);
        }

        $model = new TClaim();
        $data = $model->getList($claimMonth, null, $ids);
        $tmpPath = storage_path(self::CSV_CLAIM_PATH.'/');
        $tmpName = tempnam($tmpPath,'');
        $filePath = $tmpName.'.csv';
        $fileName = str_replace($tmpPath, '', $filePath);
        $fp = fopen($filePath, 'w');
        fputcsv($fp, $this->HEADER);

        foreach ($data as $item) {

            $claimDate = $item->claimDate === null ? '' : date_format(new DateTime($item->claimDate), 'Y/m/d');
            $paymentDate = $item->paymentDate === null ? '' : date_format(new DateTime($item->paymentDate), 'Y/m/d');
            
            $webPlanIds = $item->webPlanIds === null ? 0 : $item->webPlanIds;
            $webPlanIdUnitPrice = $item->webPlanIdUnitPrice === null ? 0 : $item->webPlanIdUnitPrice;
            $webPlanSearchUnitPrice = $item->webPlanSearchUnitPrice === null ? 0 : $item->webPlanSearchUnitPrice;
            $webPlanSearchCount = $item->webPlanSearchCount === null ? 0 : $item->webPlanSearchCount;
            $webPlanDeposit = $item->webPlanDeposit === null ? 0 : $item->webPlanDeposit;

            $apiPlanIds = $item->apiPlanIds === null ? 0 : $item->apiPlanIds;
            $apiPlanIdUnitPrice = $item->apiPlanIdUnitPrice === null ? 0 : $item->apiPlanIdUnitPrice;
            $apiPlanSearchUnitPrice = $item->apiPlanSearchUnitPrice === null ? 0 : $item->apiPlanSearchUnitPrice;
            $apiPlanSearchCount = $item->apiPlanSearchCount === null ? 0 : $item->apiPlanSearchCount;
            $apiPlanDeposit = $item->apiPlanDeposit === null ? 0 : $item->apiPlanDeposit;

            $webRow = [
                $item->companyId,
                $item->name,
                $item->claimNo,
                $claimDate,
                $paymentDate,
                $item->webPrice['totalPrice'],
                $item->postCode,
                $item->address,
                $item->tel,
                $item->claimName,
                $item->claimDepartmentJob,
                $item->claimTel,
                $item->webPlanPlanName,
                $item->webPlanTypeName,
                $webPlanIds,
                $webPlanIdUnitPrice,
                $webPlanSearchUnitPrice,
                $webPlanSearchCount,
                $apiPlanDeposit  
            ];


            $apiRow = [
                $item->companyId,
                $item->name,
                $item->claimNo,
                $claimDate,
                $paymentDate,
                $item->apiPrice['totalPrice'],
                $item->postCode,
                $item->address,
                $item->tel,
                $item->claimName,
                $item->claimDepartmentJob,
                $item->claimTel,
                $item->apiPlanPlanName,
                $item->apiPlanTypeName,
                $apiPlanIds,
                $apiPlanIdUnitPrice,
                $apiPlanSearchUnitPrice,
                $apiPlanSearchCount,
                $webPlanDeposit,
            ];
            fputcsv($fp, $webRow);
            fputcsv($fp, $apiRow);
        }
        fclose($fp);

        unlink($tmpName);

        $csvInfo = [
            'fileName'=>$fileName,
            'filePath'=>$filePath,
        ];

        return $csvInfo;
    }
}