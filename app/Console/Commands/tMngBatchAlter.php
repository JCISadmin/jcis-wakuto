<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Exception;

use function Symfony\Component\String\b;

class tMngBatchAlter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tMngBatchAlter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tMngBatch searchTypeを設定';

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
     * @throws Exception
     * @return int
     */
    public function handle(): int
    {
        Log::info('tMngBatchAlter execution');

        $baseModel = new BaseModel();
        $baseModel->begin();

        $query = DB::table('tMngBatch');
        $list = $query->get();

        try {
            foreach($list as $item){
                //json形式のsearchConditionを取得
                if(file_exists($item->searchCondition) === false ){
                    $searchCondition = json_decode($item->searchCondition , true);
                }else{
                    $jsonData = file_get_contents($item->searchCondition);
                    if (!$jsonData) {
                        $baseModel->rollback();
                        Log::error('file_get_contents() Error');
                        return -1;
                    }    
                    $jsonData = mb_convert_encoding($jsonData, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
                    $searchCondition = json_decode($jsonData , true);
                }

                if(!isset($searchCondition['cond'][0]['type'])){
                    //登記情報CSV/PDF/ZIPを'registry'に設定
                    $tMngBatch = DB::table('tMngBatch');
                    $tMngBatch->where('companyId', $item->companyId);
                    $tMngBatch->where('batchId', $item->batchId);
                    $tMngBatch->update(['searchType' => 'registry']);
                }
            }

        } catch (Exception $e) {
            $baseModel->rollback();
            throw $e;
        }

        $baseModel->commit();
        return 0;
    }
}
