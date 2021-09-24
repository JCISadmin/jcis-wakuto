<?php

namespace App\Console\Commands;

use App\Models\TMngBatch;
use Exception;
use Illuminate\Console\Command;
use App\Models\BulkSearch;
use App\Models\CsvBulkSearch;
use Illuminate\Support\Facades\Log;

class BatchBulkSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulkSearch {batchId} {companyId} {contractPlanId} {userId} {fileType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '一括検索非同期バッチ処理';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle()
    {
        Log::info('Bulk Search Start');

        $model = new BulkSearch();
        $csvModel = new CsvBulkSearch();
        $mngBatchModel = new TMngBatch();

        $batchId = $this->argument('batchId');
        $companyId = $this->argument('companyId');
        $contractPlanId = $this->argument('contractPlanId');
        $userId = $this->argument('userId');
        $fileType = $this->argument('fileType');

        $data = $model->getData($companyId, $batchId);
        if (is_null($data)) {
            Log::error('batchIdなし異常');
            return -1;
        }

        // バッチステータスを更新
        $mngBatchModel->updStatus($companyId, $batchId, '検索中', null);

        try {

            $cond = json_decode($data->searchCondition);

            $data = $model->search($cond, $batchId, $companyId, $contractPlanId, $userId, $fileType);

            $pdfData = [
                'searchData' => $data,
                'batchId' => $batchId,
                'companyId' => $companyId,
                'uploadName' => $cond->uploadName
            ];
            if ($fileType === 'application/pdf') {
                $model->makePdfFromPdf($pdfData);
            } else if ($fileType === 'application/csv') {
                $model->makePdfFromCsv($pdfData);
            } else if ($fileType === 'application/zip') {
                $model->makePdfFromPdf($pdfData);
            }

            $csvModel->makeCsv($companyId, $batchId, $fileType, $data);

        } catch (Exception $e) {
            $mngBatchModel->updStatus($companyId, $batchId, '失敗', null);

            return -1;
        }

        // バッチステータスを更新
        $mngBatchModel->updStatus($companyId, $batchId, '完了', null);

    }
}
