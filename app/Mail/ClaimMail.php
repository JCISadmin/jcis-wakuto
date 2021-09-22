<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Mail;

use App\Models\BaseModel;
use App\Models\MUserCompany;
use App\Models\MUserDetail;
use App\Models\TContractPlan;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use TCPDF;
use Datetime;
use ZipArchive;
use App\Models\Claim;

class ClaimMail extends Mailable
{
    use Queueable, SerializesModels;

    private array $data;

    /**
     * コンストラクタ
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * メール生成
     *
     * @return UserInfo
     * @throws Exception
     */
    public function build(): ClaimMail
    {
        return $this->text('mail.claim')
            ->subject(config('hds.claim.mailSubject'))
            ->with([
                'claimMonth' => $this->data['claimMonth'],
                'name' => $this->data['name'],
                'claimName' => $this->data['claimName'],
            ])
            ->attach($this->data['filePath']);
        }


}