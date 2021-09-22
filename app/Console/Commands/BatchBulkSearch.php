<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BulkSearch;

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
     */
    public function handle()
    {
        $model = new BulkSearch();

        $batchId = $this->argument('batchId');
        $companyId = $this->argument('companyId');
        $contractPlanId = $this->argument('contractPlanId');
        $userId = $this->argument('userId');
        $fileType = $this->argument('fileType');

        $data = $model->getData($companyId, $batchId);

        $cond = json_decode($data->searchCondition);

        $data = $model->search($cond, $batchId, $companyId, $contractPlanId, $userId, $fileType);
    }
}
