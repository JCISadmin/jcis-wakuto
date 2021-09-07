<?php

namespace App\Models;

use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 2要素トークン管理テーブル
 */
class T2FactToken extends BaseModel
{

    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 't2FactToken';

    /**
     * トークン作成
     *
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function createToken($userId)
    {

        $expireInterval = config('hds.auth.2factExpireInterval');
        $expireDate = new \DateTime();
        $expireDate->add(new \DateInterval($expireInterval));

        $tokenId = hash('md5', $userId . $expireDate->format(DateTimeInterface::ATOM), false);
        $authCode = $this->makePassword();

        $query = DB::table($this->table);
        $query->insert([
            'tokenId' => $tokenId,
            'authCode' => $authCode,
            'expireDate' => $expireDate->format('Y-m-d H:is')
        ]);

        return ['tokenUd' => $tokenId, 'authCode' => $authCode];

    }

}
