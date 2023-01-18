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
        //  会社情報 住所の改行文字を置換
        $this->data['companyInfo']['address'] = str_replace(array("\r\n", "\r", "\n"), "  ", $this->data['companyInfo']['address']);
        //  会社情報　郵便番号にハイフンを挿入
        $this->data['companyInfo']['postCode'] = substr_replace($this->data['companyInfo']['postCode'], '-', 3, 0);
        
        return $this->text('mail.claim')
            ->from($this->data['from'], config('mail.from.name'))
            ->subject( '【'. $this->data['claimMonth'] . config('hds.claim.mailSubject') . '】※システム自動配信メールです。')
            ->with([
                'claimMonth' => $this->data['claimMonth'],
                'name' => $this->data['name'],
                'claimName' => $this->data['claimName'],
                'chargeName' => $this->data['chargeName'],
                'companyInfo' => $this->data['companyInfo'],
            ])
            ->attach($this->data['filePath']);
    }


}
