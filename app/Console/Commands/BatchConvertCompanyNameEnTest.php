<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class BatchConvertCompanyNameEnTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchConvertCompanyNameEnTest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'BatchConvertCompanyNameEn Test';

    const CHUNK_COUNT = 1000;

    private $cnt;

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
        ' S/C Ltda',
        ',S/C Ltda',
        ' Ltda.',
        ',Ltda.',
        ' Pty Ltd.',
        ',Pty Ltd.',
        ' Pte Ltd.',
        ',Pte Ltd.',
        ' PTY. LTD.',
        ',PTY. LTD.',
        ' Pte Ltd',
        ',Pte Ltd',
        ' Ptv. Ltd',
        ',Ptv. Ltd',
        ' S/C LTDA',
        ',S/C LTDA',
        ' Company Ltd.',
        ',Company Ltd.',
        ' SOC LTD',
        ',SOC LTD',
        ' SA SOC LTD',
        ',SA SOC LTD',
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
        $this->info('BatchConvertCompanyNameEnTest START');

        $filterNameAry = $this->filterCharCompanyEn;

        $this->cnt = 0;

        DB::table('mCorporation')->chunkById(self::CHUNK_COUNT, function($mCorporation) use($filterNameAry){
            foreach($mCorporation as $record){

                foreach($filterNameAry as $filterName){
                    // フィルター文字が後方一致する場合はログ出力
                    if(preg_match('/'.$filterName.'$/', $record->inputName)){
                        $this->info('preg_match() corporationId: '.$record->corporationId);
                    }
                }
            }

            $this->cnt += self::CHUNK_COUNT;
            $this->info('Count:'.$this->cnt);

        }, 'corporationId');

        $this->info('BatchConvertCompanyNameEnTest FINISH');

        return 0;
    }

}