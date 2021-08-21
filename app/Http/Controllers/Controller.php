<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AuthUser;

/**
 * 基底コントローラー
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * アクションログ
     *
     * @param $className
     * @param $methodName
     */
    protected function actionLog($className, $methodName) {

        $userId = '';
        if (Auth::Check()) {

            /** @var $user AuthUser */
            $user = auth()->user();
            $userId = $user->userId;

        }

        Log::info(
            'METHOD IN',
            [
                'class' => $className,
                'method' => $methodName,
                'user' => $userId
            ]
        );

    }

}
