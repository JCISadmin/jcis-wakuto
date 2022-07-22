<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
     * @return ClaimMail
     */
    public function build(): ClaimMail
    {
        return $this->text('mail.claim')
            ->subject( '【'. $this->data['claimMonth'] . config('hds.claim.mailSubject') . '】※システム自動配信メールです。')
            ->with([
                'claimMonth' => $this->data['claimMonth'],
                'name' => $this->data['name'],
                'claimName' => $this->data['claimName'],
                'chargeName' => $this->data['chargeName'],
            ])
            ->attach($this->data['filePath']);
    }


}
