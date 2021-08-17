<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $user = '';
        if (Auth::Check()) {
            $user = auth()->user()->userId;
        }

        Log::info(
            'METHOD IN',
            [
                'class' => $className,
                'method' => $methodName,
                'user' => $user
            ]
        );

    }

}
