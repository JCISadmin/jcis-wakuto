<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Exception;

class BatchConvertCompanyNameEn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchConvertCompanyNameEn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '英字表記会社名を削除';

    const CHUNK_COUNT = 1000;

    private $cnt;
    private $sucCnt;

    /**
     * 会社名専用フィルター文字(英字)
     * @var array|string[]
     */
    private array $filterCharCompanyEn = [
        ' Co., Ltd.',
        ',Co., Ltd.',
        ' Co. Ltd.',
        ',Co. Ltd.',
        ' Ltd.',
        ',Ltd.',
        ' Inc.',
        ',Inc.',
        ' Corp.',
        ',Corp.',
        ' limited partnership company',
        ',limited partnership company',
        ' limited partnership',
        ',limited partnership',
        ' General Partnership Company',
        ',General Partnership Company',
        ' General Partnership',
        ',General Partnership',
        ' Unlimited Partnership Company',
        ',Unlimited Partnership Company',
        ' Unlimited Partnership',
        ',Unlimited Partnership',
        ' LLC.',
        ',LLC.',
        ' healthcare corporation',
        ',healthcare corporation',
        ' medical corporation',
        ',medical corporation',
        ' association',
        ',association',
        ' foundation',
        ',foundation',
        ' social welfare corporation',
        ',social welfare corporation',
        ' social welfare juridical person',
        ',social welfare juridical person',
        ' Specified Nonprofit Corporation',
        ',Specified Nonprofit Corporation',
        ' Approved Specified Nonprofit Corporation',
        ',Approved Specified Nonprofit Corporation',
        ' University',
        ',University',
        ' LTD',
        ',LTD',
        ' Ltd',
        ',Ltd',
        ' ltd',
        ',ltd',
        ' B.V.',
        ',B.V.',
        ' SDN.BHD.',
        ',SDN.BHD.',
        ' A.S.',
        ',A.S.',
        ' CO.',
        ',CO.',
        ' PTE. LTD.',
        ',PTE. LTD.',
        ' S.A.U.',
        ',S.A.U.',
        ' CORP S.A. DE C.V.',
        ',CORP S.A. DE C.V.',
        ' LIMITED',
        ',LIMITED',
        ' N.V',
        ',N.V',
        ' S.A.',
        ',S.A.',
        ' L.P.',
        ',L.P.',
        ' Corp.',
        ',Corp.',
        ' Corporation',
        ',Corporation',
        ' TRUST',
        ',TRUST',
        ' COMPANY',
        ',COMPANY',
        ' CORP.',
        ',CORP.',
        ' GmbH',
        ',GmbH',
        ' GMBH',
        ',GMBH',
        ' PT Pte.ltd',
        ',PT Pte.ltd',
        ' PTE',
        ',PTE',
        ' LLC',
        ',LLC',
        ' LLP',
        ',LLP',
    ];

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
        $this->info('BatchConvertCompanyNameEn START');

        $baseModel = new BaseModel();
        $filterNameAry = $this->filterCharCompanyEn;

        $baseModel->begin();
        $this->cnt = 0;
        $this->sucCnt = 0;

        DB::table('mCorporation')->chunkById(self::CHUNK_COUNT, function($mCorporation) use($baseModel, $filterNameAry){
            foreach($mCorporation as $record){

                // inputNameから英字会社名を除去
                foreach($filterNameAry as $filterName){
                    // フィルター文字が後方一致する場合は削除
                    $inputName = preg_replace('/'.$filterName.'$/', '', $record->inputName, -1, $count);

                    //一度置換を行った時点で更新
                    if($count === 1){
                        // uniCaseName(inputNameをuniCaseに変換)
                        $uniCaseName = $baseModel->convertToUniCase($inputName);

                        DB::table('mCorporation')
                        ->where('corporationId', $record->corporationId)
                        ->update([
                            'inputName' => $inputName,
                            'uniCaseName' => $uniCaseName,
                        ]);

                        $this->info(sprintf('corporationId:%d inputName: %s → %s' ,$record->corporationId, $record->inputName, $inputName));
                        $this->sucCnt++;
                        break;
                    }
                }
            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info('Count:'.$this->sucCnt. '/' .$this->cnt);

        }, 'corporationId');

        $baseModel->commit();

        $this->info('BatchConvertCompanyNameEn FINISH');
        return 0;
    }

}