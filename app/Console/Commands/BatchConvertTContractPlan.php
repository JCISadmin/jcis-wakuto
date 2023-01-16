<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use DateTime;

class BatchConvertTContractPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchConvertTContractPlan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert tContractPlan';

    const CHUNK_COUNT = 1000;

    private $cnt;

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
     *
     * @return int
     */
    public function handle()
    {
        $this->info('BatchConvertTContractPlan START');

        $baseModel = new BaseModel();

        $baseModel->begin();
        $this->cnt = 0;

        DB::table('tContractPlan')->chunkById(self::CHUNK_COUNT, function($tContractPlanAry) use($baseModel){

            $tContractPlanDetail = DB::table('tContractPlanDetail');
            $now = new DateTime();
    
            foreach($tContractPlanAry as $record){

                $tContractPlanDetail->insert([
                    'companyId' => $record->companyId,
                    'contractPlanId' => $record->contractPlanId,
                    'seqNo' => 1,
                    'contractTypeId' => $record->contractTypeId,
                    'contractStartDate' => $record->useStartDate,
                    'contractEndDate' => $record->useEndDate,
                    'idUnitPrice' => $record->idUnitPrice,
                    'searchUnitPrice' => $record->searchUnitPrice,
                    'searchCount' => $record->searchCount,
                    'createDatetime' => $now,
                    'updateDatetime' => $now,
                ]);
            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info('tContractPlan Count:'.$this->cnt);

        }, 'companyId');


        $baseModel->commit();
        $this->info('BatchConvertTContractPlan FINISH');

        return 0;
    }
}
