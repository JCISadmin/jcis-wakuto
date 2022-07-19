<?php

namespace App\Models;

use DateTime;
use Exception;

/**
 * 請求書
  */
class CsvUsageStatus extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    private array $header = array(
        '契約状況',
        '会社ID',
        '会社名',
        '当社窓口',
        'WEB:契約プラン',
        'WEB:ID個数',
        'WEB:検索件数',
        'WEB:同一ワード検索件数',
        'WEB:金額',
        'API:契約プラン',
        'API:ID個数',
        'API:検索件数',
        'API:同一ワード検索件数',
        'API:金額',
    );

    const CSV_USAGE_STATUS_PATH = 'app/csvUsageStatus';

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     *
     * @param $claimMonth
     * @param $ids
     * @return array $csvInfo
     * @throws Exception
     */
    public function makeCsv($cond, $pageNum): array
    {

        if(file_exists(storage_path(self::CSV_USAGE_STATUS_PATH)) === false){
            mkdir(storage_path(self::CSV_USAGE_STATUS_PATH), '0777');
        }

        $model = new UsageStatus();
        $data = $model->getList(
            $pageNum,
            $cond['contractPlan'],
            $cond['chargeName'],
            $cond['dispType'],
            $cond['searchDateFrom'],
            $cond['searchDateTo'],
            false
        );

        $tmpPath = storage_path(self::CSV_USAGE_STATUS_PATH.'/');
        $tmpName = tempnam($tmpPath,'');
        $filePath = $tmpName.'.csv';
        $fileName = str_replace($tmpPath, '', $filePath);
        $fp = fopen($filePath, 'w');
        fputcsv($fp, $this->header);

        foreach ($data as $item) {

            $row = [
                $item->statusName,
                $item->companyId,
                $item->name,
                $item->chargeName,
                $item->webPlanName,
                $item->webPlanIds,
                $item->webPlanTotalCount,
                $item->dupSearchCount,
                $item->webTotalPrice,
                $item->apiPlanName,
                $item->apiPlanIds,
                $item->apiPlanTotalCount,
                $item->dupSearchCount,
                $item->apiTotalPrice,
            ];
            fputcsv($fp, $row);

        }
        fclose($fp);

        unlink($tmpName);

        return [
            'fileName'=>$fileName,
            'filePath'=>$filePath,
        ];














        $model = new TClaim();
        $data = $model->getList($claimMonth, null, $ids, null, false, true);
        $tmpPath = storage_path(self::CSV_USAGE_STATUS_PATH.'/');
        $tmpName = tempnam($tmpPath,'');
        $filePath = $tmpName.'.csv';
        $fileName = str_replace($tmpPath, '', $filePath);
        $fp = fopen($filePath, 'w');
        fputcsv($fp, $this->header);

        foreach ($data as $item) {

            $claimDate = is_null($item->claimDate) ? '' : date_format(new DateTime($item->claimDate), 'Y/m/d');
            $paymentDate = is_null($item->paymentDate) ? '' : date_format(new DateTime($item->paymentDate), 'Y/m/d');
            $postCode = is_null($item->postCode) ? '' : substr_replace($item->postCode, '-', 3, 0);

            $webIds = is_null($item->webIds) ? 0 : $item->webIds;
            $webMonthSearchCount = is_null($item->webMonthSearchCount) ? 0 : $item->webMonthSearchCount;
            $webDeposit = is_null($item->webDeposit) ? 0 : $item->webDeposit;
            
            $webIdUnitPrice = '';
            $webSearchUnitPrice = '';
            foreach($item->webContractInfo as $webContractItem){
                $webIdUnitPrice .= is_null($webContractItem['idUnitPrice']) ? ' 0' : ' '.$webContractItem['idUnitPrice'];
                $webSearchUnitPrice .= is_null($webContractItem['searchUnitPrice']) ? ' 0' : ' '.$webContractItem['searchUnitPrice'];
            }

            $apiIds = is_null($item->apiIds) ? 0 : $item->apiIds;
            $apiMonthSearchCount = is_null($item->apiMonthSearchCount) ? 0 : $item->apiMonthSearchCount;
            $apiDeposit = is_null($item->apiDeposit) ? 0 : $item->apiDeposit;
            
            $apiIdUnitPrice = '';
            $apiSearchUnitPrice = '';
            foreach($item->apiContractInfo as $apiContractItem){
                $apiIdUnitPrice .= is_null($apiContractItem['idUnitPrice']) ? ' 0' : ' '.$apiContractItem['idUnitPrice'];
                $apiSearchUnitPrice .= is_null($apiContractItem['searchUnitPrice']) ? ' 0' : ' '.$apiContractItem['searchUnitPrice'];
            }

            $row = [
                $item->companyId,
                $item->name,
                $item->claimNo,
                $claimDate,
                $paymentDate,
                $item->priceWithTax,
                $postCode,
                $item->address,
                $item->tel,
                $item->claimName,
                $item->claimDepartmentJob,
                $item->claimTel,
                $item->webContractPlanName,
                $item->webContractTypeName,
                $webIds,
                $webIdUnitPrice,
                $webSearchUnitPrice,
                $webMonthSearchCount,
                $webDeposit,
                $item->apiContractPlanName,
                $item->apiContractTypeName,
                $apiIds,
                $apiIdUnitPrice,
                $apiSearchUnitPrice,
                $apiMonthSearchCount,
                $apiDeposit,
            ];
            fputcsv($fp, $row);
        }
        fclose($fp);

        unlink($tmpName);

        return [
            'fileName'=>$fileName,
            'filePath'=>$filePath,
        ];

    }
}
