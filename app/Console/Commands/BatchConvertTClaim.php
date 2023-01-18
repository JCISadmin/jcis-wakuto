<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use DateTime;

class BatchConvertTClaim extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchConvertTClaim';

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
        $this->info('BatchConvertTClaim START');

        $baseModel = new BaseModel();

        $baseModel->begin();
        $this->cnt = 0;

        DB::table('mUserCompany')->chunkById(self::CHUNK_COUNT, function($mUserCompanyAry){

            $now = new DateTime();

            foreach($mUserCompanyAry as $record){
                
                $tClaim = DB::table('tClaim');
                $tClaim->where('companyId', $record->companyId);

                $tClaim->update([
                    'claimNote' => config('note.claim.claimNote'),
                    'name' => $record->name,
                    'postCode' => $record->postCode,
                    'address' => $record->address,
                    'tel' => $record->tel,
                    'chargeName' => $record->chargeName,
                    'chargeMail' => $record->chargeMail,
                    'claimName' => $record->claimName,
                    'claimDepartmentJob' => $record->claimDepartmentJob,
                    'claimTel' => $record->claimTel,
                    'claimMailTo' => $record->claimMailTo,
                    'claimMailCc' => $record->claimMailCc,
                    'claimMailBcc' => $record->claimMailBcc,
                    'updateDatetime' => $now,
                ]);
            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info('companyId Count:'.$this->cnt);

        }, 'companyId');


        $baseModel->commit();
        $this->info('BatchConvertTClaim FINISH');

        return 0;
    }
}
