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
        'WEB:金額',
        'API:契約プラン',
        'API:ID個数',
        'API:検索件数',
        'API:金額',
        '同一ワード検索件数',
        'Acuris一覧:検索数',
        'Acuris詳細:検索数',
        'Acuris一覧:金額',
        'Acuris詳細:金額',
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
        fwrite($fp, "\xEF\xBB\xBF");
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
                $item->webTotalPrice,
                $item->apiPlanName,
                $item->apiPlanIds,
                $item->apiPlanTotalCount,
                $item->apiTotalPrice,
                $item->dupSearchCount,
                $item->acurisTotalCount,
                $item->acurisDetailTotalCount,
                $item->acurisTotalPrice,
                $item->acurisDetailTotalPrice,
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
