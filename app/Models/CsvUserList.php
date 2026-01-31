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
                $webPlanUseEndAlertDate = date_format(new \DateTime($item->webPlanUseEndAlertDate), 'Y/m/d');

            }

            if (isset($item->webPlanUseEndDate)) {
                $webPlanUseEndDate = date_format(new \DateTime($item->webPlanUseEndDate), 'Y/m/d');
            }

            if (isset($item->apiPlanUseEndAlertDate)) {
                $apiPlanUseEndAlertDate = date_format(new \DateTime($item->apiPlanUseEndAlertDate), 'Y/m/d');
            }

            if (isset($item->apiPlanUseEndDate)) {
                $apiPlanUseEndDate = date_format(new \DateTime($item->apiPlanUseEndDate), 'Y/m/d');
            }

            $row = [
                $item->statusName,
                $item->companyId,
                $item->name,
                $item->staffName,
                $item->chargeName,
                $item->webPlanName,
                $item->webPlanIds,
                $webPlanUseEndAlertDate,
                $webPlanUseEndDate,
                $item->apiPlanName,
                $item->apiPlanIds,
                $apiPlanUseEndAlertDate,
                $apiPlanUseEndDate,
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
