<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

/**
 * 基底モデル
 */
class BaseModel extends Model
{
    const DEL_FLG_ON = 1;
    const DEL_FLG_OFF = 0;

    const LOCK_FLG_ON = 1;
    const LOCK_FLG_OFF = 0;

    const PAGE_LINE = 10;

    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    const CLAIM_STATUS_DONE = 1;
    const CLAIM_STATUS_UNDONE = 0;

    const PAYMENT_STATUS_DONE = 1;
    const PAYMENT_STATUS_UNDONE = 0;

    const DEPOSIT_USE_PLAN_TYPE = 'allDepo';

    const ITEM_TRIAL = 'ID発行及び利用料（トライアル期間のため無料）';
    const ITEM_DEPOSIT = '法人名・個人名検索';
    const ITEM_PAYPERUSE = '法人名・個人名検索';
    const ITEM_ID = 'ID発行及び利用料';
    const ITEM_SHORTAGE = '法人名・個人名検索（デポジット不足）';

    const CHARGE_FLG_ON = 1;
    const CHARGE_FLG_OFF = 0;

    const PLAN_TYPE_WEB = 'web';
    const PLAN_TYPE_API = 'api';

    const PREPAID_DONE = 1;
    const PREPAID_UNDONE = 0;

    const STATUS_TRIAL = 1;//トライアル
    const STATUS_CONTRACT = 2;//契約中
    const STATUS_END = 3;//契約終了

    /**
     * パスワード生成
     *
     * @return string
     */
    public function makePassword(): string
    {

        $charAry = [
            '0', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            '0', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            '0', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        ];

        $pwd = '';
        $rndMax = count($charAry);

        for ($i = 0; $i < 8; $i++) {
            $pwd .= $charAry[mt_rand() % $rndMax];
        }

        return $pwd;
    }


    /**
     * トランザクション Start
     *
     * @throws Exception
     */
    public function begin() {
        $this->tranLog('BEGIN');
        DB::beginTransaction();
    }

    /**
     * トランザクション Commit
     *
     * @throws Exception
     */
    public function commit() {
        $this->tranLog('COMMIT');
        DB::commit();
    }

    /**
     * トランザクション Rollback
     *
     * @throws Exception
     */
    public function rollback() {
        $this->tranLog('ROLLBACK');
        DB::rollBack();
    }


    /**
     * トランザクションログ出力
     * @param $type
     */
    private function tranLog($type) {

        $userId = '';
        if (App::runningInConsole() === false) {
            if (Auth::Check()) {
                /** @var $user AuthUser */
                $user = auth()->user();
                $userId = $user->userId;
            }
        }

        Log::info('DB TRAN ' . $type, ['user' => $userId]);

    }

    /**
     * 日付チェック関数
     *
     * @param $str
     * @return bool
     */
    protected function checkDate($str)
    {
        $errFlag = false;
        $aryStr = explode('/', $str);

        if($aryStr[0] !== '' && $aryStr[1] !== '' && $aryStr[2] !== ''){  
            if (count($aryStr) === 3) {
                if (checkdate($aryStr[1], $aryStr[2], $aryStr[0]) === false) {
                    $errFlag = true;
                }
            } else {
                $errFlag = true;
            }
        }else{
            $errFlag = true;
        }
        
        return $errFlag;

    }


}
