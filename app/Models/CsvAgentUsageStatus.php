<?php

namespace App\Models;

use Exception;
use DateTime;

/**
 * CSV利用状況
  */
class CsvAgentUsageStatus extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    private array $header = array(
        'No',
        '会社名',
        'WEB:利用開始日',
        'WEB:契約プラン',
        'WEB:ID個数',
        'WEB:検索件数',
        'API:利用開始日',
        'API:契約プラン',
        'API:ID個数',
        'API:検索件数',
        'Acuris一覧:検索数',
        'Acuris詳細:検索数',
    );

    const CSV_AGENT_USAGE_STATUS_PATH = 'app/csvAgentUsageStatus';

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

        if(file_exists(storage_path(self::CSV_AGENT_USAGE_STATUS_PATH)) === false){
            mkdir(storage_path(self::CSV_AGENT_USAGE_STATUS_PATH), '0777');
        }

        $model = new AgentUsageStatus();
        $startOfMonth = new DateTime($cond['targetMonth'] . '-01');
        $endOfMonth = (clone $startOfMonth)->modify('last day of this month');
        $startDate = $startOfMonth->format('Y-m-d 00:00:00');
        $endDate = $endOfMonth->format('Y-m-d 23:59:59');

        $data = $model->getList(
            $pageNum,
            $cond['contractPlan'],
            "",
            $cond['dispType'],
            $startDate,
            $endDate,
            true,
            $cond['agentNo']
        );

        $tmpPath = storage_path(self::CSV_AGENT_USAGE_STATUS_PATH.'/');
        $tmpName = tempnam($tmpPath,'');
        $filePath = $tmpName.'.csv';
        $fileName = str_replace($tmpPath, '', $filePath);
        $fp = fopen($filePath, 'w');
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, $this->header);

        $no = 1;

        foreach ($data as $item) {

            // 会社名マスク
            $maskedCompanyName = mb_substr($item->name, 0, 2) . str_repeat('*', mb_strlen($item->name) - 3) . mb_substr($item->name, -1);

            $row = [
                $no,
                $maskedCompanyName,
                $item->webUseStartDate,
                $item->webPlanName,
                $item->webPlanIds,
                $item->webSearchCount,
                $item->apiUseStartDate,
                $item->apiPlanName,
                $item->apiPlanIds,
                $item->apiSearchCount,
                $item->acurisTotalCount,
                $item->acurisDetailTotalCount,
            ];
            fputcsv($fp, $row);
            $no++;

        }
        fclose($fp);

        unlink($tmpName);

        return [
            'fileName'=>$fileName,
            'filePath'=>$filePath,
        ];

    }
}
