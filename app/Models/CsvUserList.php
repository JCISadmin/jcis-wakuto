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
        '当社窓口',
        '当社窓口E-MAIL',
        '契約状況',
        '会社ID',
        '会社名',
        'カナ',
        '代表電話番号',
        '郵便番号',
        '会社住所',
        '会社代表',
        '担当者',
        '担当部署',
        '担当者電話番号',
        '担当者E-MAIL',
        '請求者名',
        '請求者部署',
        '請求者電話番号',
        '請求先TO',
        '請求先CC',
        '請求先BCC',
        '支払期限',
        '送付期限',
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
    public function makeCsv($cond, $pageNum, $scope= 'page'): array
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
            $cond['page'],
            $scope
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
                $item->chargeName,
                $item->chargeMail,
                $item->statusName,
                $item->companyId,
                $item->name,
                $item->kana,
                $item->tel,
                $item->postCode,
                $item->address,
                $item->president,
                $item->staffName,
                $item->staffDepartmentJob,
                $item->staffTel,
                $item->staffMail,
                $item->claimName,
                $item->claimDepartmentJob,
                $item->claimTel,
                $item->claimMailTo,
                $item->claimMailCc,
                $item->claimMailBcc,
                $item->paymentTerm,
                $item->deliveryDate,
                $item->webPlanName,
                $item->webPlanIds,
                $webPlanUseEndAlertDate,
                $webPlanUseEndDate,
                $item->apiPlanName,
                $item->apiPlanIds,
                $apiPlanUseEndAlertDate,
                $apiPlanUseEndDate,
                $item->memo,
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
