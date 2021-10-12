<?php

namespace App\Models;

use DateTime;
use Exception;

/**
 * 請求書
  */
class CsvClaim extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    private array $header = array(
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
        'WEB:契約プラン',
        'WEB:契約形態',
        'WEB:ID個数',
        'WEB:ID代',
        'WEB:検索単価',
        'WEB:月間検索数',
        'WEB:デポジット残額',
        'API:契約プラン',
        'API:契約形態',
        'API:ID個数',
        'API:ID代',
        'API:検索単価',
        'API:月間検索数',
        'API:デポジット残額',
    );

    const CSV_CLAIM_PATH = 'app/csvClaim';

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     *
     * @param $claimMonth
     * @param $ids
     * @return array $csvInfo
     * @throws Exception
     *
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function makeCsv($claimMonth, $ids): array
    {

        if(file_exists(storage_path(self::CSV_CLAIM_PATH)) === false){
            mkdir(storage_path(self::CSV_CLAIM_PATH), '0777');
        }

        $model = new TClaim();
        $data = $model->getList($claimMonth, null, $ids, null, false, true);
        $tmpPath = storage_path(self::CSV_CLAIM_PATH.'/');
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
            $webIdUnitPrice = is_null($item->webIdUnitPrice) ? 0 : $item->webIdUnitPrice;
            $webSearchUnitPrice = is_null($item->webSearchUnitPrice) ? 0 : $item->webSearchUnitPrice;
            $webMonthSearchCount = is_null($item->webMonthSearchCount) ? 0 : $item->webMonthSearchCount;
            $webDeposit = is_null($item->webDeposit) ? 0 : $item->webDeposit;

            $apiIds = is_null($item->apiIds) ? 0 : $item->apiIds;
            $apiIdUnitPrice = is_null($item->apiIdUnitPrice) ? 0 : $item->apiIdUnitPrice;
            $apiSearchUnitPrice = is_null($item->apiSearchUnitPrice) ? 0 : $item->apiSearchUnitPrice;
            $apiMonthSearchCount = is_null($item->apiMonthSearchCount) ? 0 : $item->apiMonthSearchCount;
            $apiDeposit = is_null($item->apiDeposit) ? 0 : $item->apiDeposit;

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
