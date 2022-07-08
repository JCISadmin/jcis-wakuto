<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;

class convertContractPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchConvertContractPlan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert tContractPlan';

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
        $this->info('BatchConvertContractPlan START');

        $baseModel = new BaseModel();

        $baseModel->begin();

        $tContractPlan = DB::table('tContractPlan');
        $tContractPlanAry = $tContractPlan->get();

        $tContractPlanDetail = DB::table('tContractPlanDetail');

        foreach($tContractPlanAry as $record){

            $now = new DateTime();

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

        $baseModel->commit();
        $this->info('BatchConvertContractPlan FINISH');

        return 0;
    }
}
