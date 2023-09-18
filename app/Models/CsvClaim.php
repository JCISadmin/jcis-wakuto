<?php

namespace App\Models;

use DateTime;
use Exception;

/**
 * CSV請求
  */
class CsvClaim extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    const CSV_CLAIM_PATH = 'app/csvClaim';

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     * misoca用 CSV生成
     *
     * @param $claimMonth
     * @return array $csvInfo
     * @throws Exception
     *
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function makeCsv($claimMonth): array
    {

        if(file_exists(storage_path(self::CSV_CLAIM_PATH)) === false){
            mkdir(storage_path(self::CSV_CLAIM_PATH), '0777');
        }

        $model = new TClaim();
        $detailModel = new TClaimDetail();
        $data = $model->getList($claimMonth, null, null, null, false, true);

        $tmpPath = storage_path(self::CSV_CLAIM_PATH.'/');
        $filePath = $tmpPath . 'invoice-' . date('YmdHis') . '.csv';
        $fileName = str_replace($tmpPath, '', $filePath);
        $fp = fopen($filePath, 'w+');

        $colMax = $detailModel->getCsvColumn($claimMonth);
        $headAry = $this->makeHeader($colMax);
        mb_convert_variables('SJIS-win', 'UTF-8', $headAry);
        fputcsv($fp, $headAry);

        foreach ($data as $item) {

            $csvColumn = [];

            // 請求日
            $csvColumn[] = date_format(new DateTime($item->claimDate), 'Y/m/d');

            // 請求番号
            $csvColumn[] = '';

            // 件名
            $csvColumn[] = '';

            // 取引先管理コード：会社ID
            $csvColumn[] = $item->companyId;

            // 消費税設定
            $csvColumn[] = 'EXCLUDE';

            // お支払い期限
            $csvColumn[] = date_format(new DateTime($item->paymentDate), 'Y/m/d');

            // 登録番号
            $csvColumn[] = config('hds.claim.invoiceNo');

            $detailData = $detailModel->getCsvData($item->companyId, $claimMonth);

            foreach($detailData as $line) {
                // 納品日
                $csvColumn[] = '';

                // 品名
                $csvColumn[] = $line->itemName;

                if ($line->type == 'title') {
                    // 数量
                    $csvColumn[] = '';

                    // 単位
                    $csvColumn[] = '';

                    // 単価
                    $csvColumn[] = '';

                    // 消費税
                    $csvColumn[] = '';

                } else {
                    // 数量
                    $csvColumn[] = $line->amount;

                    // 単位
                    $csvColumn[] = $line->unit;

                    // 単価
                    $csvColumn[] = $line->unitPrice;

                    // 消費税
                    $csvColumn[] = '10';

                }

                // 非課税フラグ
                $csvColumn[] = '';

            }

            $cntMax = $colMax - count($detailData);
            for ($i = 0; $i < $cntMax; $i++) {
                // 納品日
                $csvColumn[] = '';

                // 品名
                $csvColumn[] = '';

                // 数量
                $csvColumn[] = '';

                // 単位
                $csvColumn[] = '';

                // 単価
                $csvColumn[] = '';

                // 消費税
                $csvColumn[] = '';

                // 非課税フラグ
                $csvColumn[] = '';

            }

            mb_convert_variables('SJIS-win', 'UTF-8', $csvColumn);
            fputcsv($fp, $csvColumn);
        }
        fclose($fp);

        return [
            'fileName'=>$fileName,
            'filePath'=>$filePath,
        ];

    }

    /**
     * CSVヘッダー行生成
     *
     * @param $cols
     * @return string[]
     */
    private function makeHeader($cols) {
        $headAry = [
            '請求日',
            '請求番号',
            '件名',
            '取引先管理コード',
            '消費税設定',
            'お支払い期限',
            '登録番号',
        ];

        $cnt = 20;
        if ($cols > 20) {
            $cnt = $cols;
        }

        for ($i = 1; $i <= $cnt; $i++) {
            $headAry[] = '納品日' . $i;
            $headAry[] = '品目' . $i;
            $headAry[] = '数量' . $i;
            $headAry[] = '単位' . $i;
            $headAry[] = '単価' . $i;
            $headAry[] = '消費税率' . $i;
            $headAry[] = '非課税フラグ' . $i;
        }

        return $headAry;
    }

}
