<?php

namespace App\Models;

use DateInterval;
use DateTime;
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
     * 認証OKUserId
     *
     * @var string
     */
    public string $authUserId;


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
    public function createToken($userId): array
    {

        $expireInterval = config('hds.auth.2factExpireInterval');
        $expireDate = new DateTime();
        $expireDate->add(new DateInterval($expireInterval));

        $tokenId = hash('md5', $userId . $expireDate->format(DateTimeInterface::ATOM), false);
        $authCode = $this->makePassword();

        $query = DB::table($this->table);
        $query->insert([
            'tokenId' => $tokenId,
            'authCode' => $authCode,
            'userId' => $userId,
            'expireDate' => $expireDate->format('Y-m-d H:i:s')
        ]);

        return ['tokenId' => $tokenId, 'authCode' => $authCode];

    }

    /**
     * 2要素認証処理
     *
     * @param $tokenId
     * @param $authCode
     * @return bool
     */
    public function authCodeCheck($tokenId, $authCode): bool
    {

        $dt = new DateTime();
        $now = $dt->format('Y-m-d h:i:s');

        $query = DB::table($this->table);
        $query->where('tokenId', $tokenId);
        $query->where('authCode', $authCode);
        $query->where('expireDate', '>', $now);

        $rec = $query->get();
        if (count($rec) == 0) {
            $this->authUserId = null;
            return false;
        }

        $this->authUserId = $rec[0]->userId;

        return true;

    }



}
