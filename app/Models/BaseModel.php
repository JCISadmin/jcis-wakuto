<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 基底モデル
 */
class BaseModel extends Model
{
    const DEL_FLG_ON = 1;
    const DEL_FLG_OFF = 0;

    const LOCK_FLG_ON = 1;
    const LOCK_FLG_OFF = 0;

}
