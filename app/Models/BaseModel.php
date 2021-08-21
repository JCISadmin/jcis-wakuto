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

        $user = '';
        if (App::runningInConsole() === false) {
            if (Auth::Check()) {
                $user = auth()->user()->userId;
            }
        }

        Log::info('DB TRAN ' . $type, ['user' => $user]);

    }

}
