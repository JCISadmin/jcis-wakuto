<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Exception;

class BatchConvertUnicase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchConvertUniCase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'uniCaseName/uniCaseKana を設定';

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
     * @throws Exception
     * @return int
     */
    public function handle(): int
    {
        $this->info('BatchConvertUniCase START');

        $baseModel = new BaseModel();

        //mCorporation
        $this->info('mCorporation START');

        $baseModel->begin();
        $this->cnt = 0;

        DB::table('mCorporation')->chunkById(self::CHUNK_COUNT, function($mCorporation) use($baseModel){
                foreach($mCorporation as $record){

                    //uniCaseName(inputNameをuniCaseに変換)
                    $uniCaseName = $baseModel->convertToUniCase($record->inputName);

                    DB::table('mCorporation')
                    ->where('corporationId', $record->corporationId)
                    ->update(['uniCaseName' => $uniCaseName]);

                }

                $this->cnt += self::CHUNK_COUNT;
                $this->info('mCorporation Count:'.$this->cnt);

        }, 'corporationId');

        $baseModel->commit();
        $this->info('mCorporation FINISH');

        //mPerson
        $this->info('mPerson START');

        $baseModel->begin();
        $this->cnt = 0;

        DB::table('mPerson')->chunkById(self::CHUNK_COUNT, function($mPerson) use($baseModel){
            foreach($mPerson as $record){

                //uniCaseName(inputNameをuniCaseに変換)
                $uniCaseName = $baseModel->convertToUniCase($record->inputName);
                //uniCaseKana(inputKanaをuniCaseに変換)
                $uniCaseKana = $baseModel->convertToUniCase($record->inputKana);

                DB::table('mPerson')
                ->where('personId', $record->personId)
                ->update([
                    'uniCaseName' => $uniCaseName,
                    'uniCaseKana' => $uniCaseKana,
                ]);

            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info('mPerson Count:'.$this->cnt);

        }, 'personId');

        $baseModel->commit();
        $this->info('mPerson FINISH');

        $this->info('BatchConvertUniCase FINISH');

        return 0;

    }

}