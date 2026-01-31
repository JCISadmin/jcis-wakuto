<?php

namespace App\Models;

use Exception;

/**
 * CSV利用状況
  */
class CsvUserList extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    private array $header = array(
        '契約状況',
        '会社ID',
        '会社名',
        '担当者',
        '当社窓口',
        'Web:契約プラン',
        'Web：ID数',
        'Web：終了通知日',
        'Web：終了予定日',
        'API:契約プラン',
        'API：ID数',
        'API：終了通知日',
        'API：終了予定日',
        'メモ',
    );

    const CSV_USER_PATH = 'app/csvUser';

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

        if(file_exists(storage_path(self::CSV_USER_PATH)) === false){
            mkdir(storage_path(self::CSV_USER_PATH), '0777');
        }

        $model = new MUserCompany();

        $data = $model->getList(
            $cond['companyName'],
            $cond['contractStatus'],
            $cond['contractPlan'],
            $cond['useEndAlertDate'],
            $pageNum,
			$cond['agentinfo'],
            $cond['page']
        );

        $tmpPath = storage_path(self::CSV_USER_PATH.'/');
        $filePath = $tmpPath . 'user_' . date('YmdHis') . '.csv';
        $fileName = str_replace($tmpPath, '', $filePath);

        $fp = fopen($filePath, 'w');
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, $this->header);

        foreach ($data as $item) {
            $webPlanUseEndAlertDate = "";
            $webPlanUseEndDate = "";
            $apiPlanUseEndAlertDate = "";
            $apiPlanUseEndDate = "";

            if (isset($item->webPlanUseEndAlertDate)) {
                $webPlanUseEndAlertDate = new \DateTime($item->webPlanUseEndAlertDate);
            }

            if (isset($item->webPlanUseEndDate)) {
                $webPlanUseEndDate = new \DateTime($item->webPlanUseEndDate);
            }

            if (isset($item->apiPlanUseEndAlertDate)) {
                $apiPlanUseEndAlertDate = new \DateTime($item->apiPlanUseEndAlertDate);
            }

            if (isset($item->apiPlanUseEndDate)) {
                $apiPlanUseEndDate = new \DateTime($item->apiPlanUseEndDate);
            }

            $row = [
                $item->statusName,
                $item->companyId,
                $item->name,
                $item->staffName,
                $item->chargeName,
                $item->webPlanName,
                $item->webPlanIds,
                date_format($webPlanUseEndAlertDate, 'Y/m/d'),
                date_format($webPlanUseEndDate, 'Y/m/d'),
                $item->apiPlanName,
                $item->apiPlanIds,
                date_format($apiPlanUseEndAlertDate, 'Y/m/d'),
                date_format($apiPlanUseEndDate, 'Y/m/d'),
            ];
            fputcsv($fp, $row);

        }
        fclose($fp);

        // unlink($tmpName);

        return [
            'fileName'=>$fileName,
            'filePath'=>$filePath,
        ];

    }
}
