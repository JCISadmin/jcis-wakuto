<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class tMngBatchAlterTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tMngBatchAlterTest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tMngBatchAlter DBチェック';

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
        Log::info('tMngBatchAlterTest 実行');

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
                        Log::error('file_get_contents() Error');
                        return -1;
                    }
                    $jsonData = mb_convert_encoding($jsonData, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
                    $searchCondition = json_decode($jsonData , true);
                }

                if(is_null($searchCondition)){
                    Log::error('検索条件が取得できていません companyId:'.$item->companyId.' batchId:'.$item->batchId);
                    continue;
                }

                if($item->searchType === 'normal'){
                    if(!isset($searchCondition['cond'][0]['type'])){
                        Log::error('無効なCSV一括検索データ companyId:'.$item->companyId.' batchId:'.$item->batchId);
                    }

                }elseif($item->searchType === 'registry'){
                    if(!isset($searchCondition['cond'][0][0]['fileName'])){
                        Log::error('無効な登記情報検索データ companyId:'.$item->companyId.' batchId:'.$item->batchId);
                    }

                }else{
                    Log::error('searchTypeエラー companyId:'.$item->companyId.' batchId:'.$item->batchId);
                
                }
            }
            Log::info('tMngBatchAlterTest 完了');
            
        } catch (Exception $e) {
            throw $e;
        }

        return 0;
    }
}
