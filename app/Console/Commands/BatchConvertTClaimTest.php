<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use DateTime;

class BatchConvertTClaimTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchConvertTClaimTest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert tClaim';

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
        $this->info('BatchConvertTClaimTest START');

        $baseModel = new BaseModel();

        $baseModel->begin();
        $this->cnt = 0;

        DB::table('tClaim')->chunkById(self::CHUNK_COUNT, function($tClaimAry){

            foreach($tClaimAry as $record){
                
                $mUserCompany = DB::table('mUserCompany');
                $mUserCompany->where('companyId', $record->companyId);
                $mUserRecord = $mUserCompany->first();

                if($record->name !== $mUserRecord->name){
                    $this->info('data Error: companyId '.$mUserRecord->companyId);
                }

            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info('tClaim Count:'.$this->cnt);

        }, 'companyId');


        $baseModel->commit();
        $this->info('BatchConvertTClaimTest FINISH');

        return 0;
    }
}
