<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use DateTime;
use App\Models\TClaim;
use App\Models\Claim;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use App\Models\TKeywordHistory;
use App\Models\MVat;
use App\Models\TContractPlan;
use App\Models\TClaimDetail;

class BatchFixTClaimDetail20221004 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchFixTClaimDetail20221004';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'BatchFixTClaimDetail20221004';

    const CHUNK_COUNT = 50000;

    private $cnt;
    private $sucCnt;


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
        $this->info('BatchFixTClaimDetail20221004 START');

        $baseModel = new BaseModel();
        $baseModel->begin();

        $this->cnt = 0;
        $this->sucCnt = 0;

        DB::table('tClaim')
            ->where('claimStatus', 1)
            ->whereIn('claimMonth', [202208,202209,202210])
            ->orderBy('claimMonth')
            ->chunk(self::CHUNK_COUNT, function($tClaim){

            $now = new DateTime();
            $claim = new Claim();

            foreach($tClaim as $record){
                
                $this->info($record->companyId);
                $query = DB::table('tClaimDetail');
                $query->select(DB::raw('count(*) as count'));
                $query->where('companyId', $record->companyId);
                $query->where('claimMonth', $record->claimMonth);
                $count = $query->first();

                // 既存tClaimDetailレコードが無い場合、データを作成

                if($count->count === 0){

                    $companyIds = [];
                    $companyIds[] = $record->companyId;

                    DB::table('tClaim')->where('companyId', $record->companyId)
                                        ->where('claimMonth', $record->claimMonth)
                                        ->delete();

                    //tClaimDetailに追加
                    $date = new DateTime($record->claimMonth.'01');
                    $claimMonth = date_format($date, 'Y-m');
                    $expenseList = $claim->getExpenseList($companyIds, $claimMonth);

                    $num = 1;
                    foreach($expenseList as $item){

                        $ins = DB::table('tClaimDetail');
                        $ins->insert([
                            'companyId' => $record->companyId,
                            'claimMonth' => $record->claimMonth,
                            'seqNo' => $num,
                            'type' => $item['type'],
                            'useFlg' => $item['useFlg'],
                            'itemName' => $item['itemName'],
                            'amount' => $item['amount'],
                            'unit' => $item['unit'],
                            'unitPrice' => $item['unitPrice'],
                            'price' => $item['price'],
                            'createDatetime' => $now,
                            'updateDatetime' => $now
                        ]);
        
                        $num++;
                    }

                    $ins = DB::table('tClaim');
                    $ins->insert([
                        'companyId' => $record->companyId,
                        'claimMonth' => $record->claimMonth,
                        'claimNo' => $record->claimNo,
                        'price' => $record->price,
                        'claimDate' => $record->claimDate,
                        'paymentDate' => $record->paymentDate,
                        'deliveryDate' => $record->deliveryDate,
                        'claimStatus' => $record->claimStatus,
                        'paymentStatus' => $record->paymentStatus,
                        'webPrepaidStatus' => $record->webPrepaidStatus,
                        'apiPrepaidStatus' => $record->apiPrepaidStatus,
                        'memo' => $record->memo,
                        'claimNote' => $record->claimNote,
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
                        'createDatetime' => $record->createDatetime,
                        'updateDatetime' => $now,

                    ]);

                    $this->info('tClaimDetail Insert companyId:'.$record->companyId);
                    $this->sucCnt++;
                }
            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info('record Count:'.$this->sucCnt.'/'.$this->cnt);

        });

        $baseModel->commit();
        $this->info('BatchFixTClaimDetail20221004 FINISH');

        return 0;
    }


}
