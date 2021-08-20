<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
     * トランザクション Start
     *
     * @throws \Exception
     */
    public function begin() {
        $this->tranLog('BEGIN');
        DB::beginTransaction();
    }

    /**
     * トランザクション Commit
     *
     * @throws \Exception
     */
    public function commit() {
        $this->tranLog('COMMIT');
        DB::commit();
    }

    /**
     * トランザクション Rollback
     *
     * @throws \Exception
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
        if (\App::runningInConsole() === false) {
            if (Auth::Check()) {
                $user = auth()->user()->userId;
            }
        }

        Log::info('DB TRAN ' . $type, ['user' => $user]);

    }


}
