<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Exception;

class BatchConvertUniCase extends Command
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
        $this->info('BatchConvertUniCase execution');

        //mCorporation
        $this->info('mCorporation start');

        $baseModel = new BaseModel();
        $baseModel->begin();

        $mCorporation = DB::table('mCorporation');
        $corporationAry = $mCorporation->select('corporationId', 'inputName')->get();

        foreach($corporationAry as $record){
            //uniCaseName(inputNameをuniCaseに変換)
            $uniCaseName = $baseModel->convertToUniCase($record->inputName);

            $table = DB::table('mCorporation');
            $table->where('corporationId', $record->corporationId)
                         ->update(['uniCaseName' => $uniCaseName]);
        }    

        $baseModel->commit();
        $this->info('mCorporation finish');


        //mPerson
        $this->info('mPerson start');

        $baseModel->begin();

        $mPerson = DB::table('mPerson');
        $personAry = $mPerson->select('personId', 'inputName', 'inputKana')->get();

        foreach($personAry as $record){
            //uniCaseName(inputNameをuniCaseに変換)
            $uniCaseName = $baseModel->convertToUniCase($record->inputName);
            //uniCaseKana(inputKanaをuniCaseに変換)
            $uniCaseKana = $baseModel->convertToUniCase($record->inputKana);

            $table = DB::table('mPerson');
            $table->where('personId', $record->personId)
                         ->update([
                            'uniCaseName' => $uniCaseName,
                            'uniCaseKana' => $uniCaseKana,
                        ]);
        }    

        $baseModel->commit();
        $this->info('mPerson finish');

        return 0;

    }

}