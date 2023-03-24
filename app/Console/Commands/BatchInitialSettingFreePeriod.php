<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BaseModel;
use DateTime;
use DB;


class BatchInitialSettingFreePeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settingFreePeriod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'settingFreePeriod';

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

        $this->cnt = 0;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('settingFreePeriod START');

        $baseModel = new BaseModel();
        $baseModel->begin();

        // 会社マスタの無料期間を設定(mddを除いて 一律1年間)
        $mUserCompany = DB::table('mUserCompany');
        $mUserCompany->update([
            'freeFlg' => 1,
            'freePeriod' => 366
        ]);
        // 会社マスタの無料期間を設定(mddのみ無制限で設定)
        $mUserCompany = DB::table('mUserCompany');
        $mUserCompany->where('companyId', 'mdd');
        $mUserCompany->update([
            'freeFlg' => 2,
            'freePeriod' => 0
        ]);
        $this->info("mUserCompany update");


        // 検索履歴の無料期間満了日を設定(mdd含め 一律1年後)
        DB::table('tKeywordHistory')->chunkById(self::CHUNK_COUNT, function($tKeywordHistoryAry){

            foreach($tKeywordHistoryAry as $record){
                
                $searchDate = new DateTime($record->searchDate);

                // 満了日を検索日 + 366日 - 1日 で設定
                $expireDate = $searchDate->modify("+366 day")->modify("-1 day")->format('YmdHisv');

                $upd = DB::table('tKeywordHistory');
                $upd->where('companyId', $record->companyId);
                $upd->where('contractPlanId', $record->contractPlanId);
                $upd->where('userId', $record->userId);
                $upd->where('hash', $record->hash);

                $upd->update([
                    'expireDate' => $expireDate,
                ]);
            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info("tKeywordHistory Count:$this->cnt");

        }, 'companyId');

        $baseModel->commit();

        $this->info('settingFreePeriod FINISH');
        return 0;
    }
}
