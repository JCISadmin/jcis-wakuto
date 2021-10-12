<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MUserDetail;
use App\Models\UseReport;

/**
 * 利用明細用API
 */
class UseReportController extends Controller
{

    /**
     * APIプランを認証し、利用明細を取得する
     *
     * @param Request $request
     * @return string
     */
    public function authUseReport(Request $request): string
    {
        $authModel = new MUserDetail();
        $useReportModel = new UseReport();

        // バリデーションを行う
        // 必須項目の確認
        $errorCode = "";
        if ($request->all() === []) {
            $errorCode = "e001";
        } else if (!isset($request['id'])) {
            $errorCode = "e002";
        } else if (!isset($request['password'])) {
            $errorCode = "e003";
        }

        if($errorCode !== "") {
            $response = [
                "status" => "NG",
                "code" => $errorCode,
                "query" => NULL,
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
        }


        // 認証を行う
        $authData = $authModel->getUserCredentialsApi($request['id'], $request['password']);
        if (is_null($authData)) {

            $response = [
                "status" => "NG",
                "code" => "w001",
                "monthSearchCount" => NULL,
                "yearSearchCount" => NULL,
                "depositBalance" => NULL,
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
        }


        // 利用明細を取得する
        $userData = $useReportModel->getList($authData->companyId, $authData->userId);

        $response = [
            "status" => "OK",
            "code" => "i001",
            "monthSearchCount" => $userData['monthSearchCount'],
            "yearSearchCount" => $userData['yearSearchCount'],
            "depositBalance" => $userData['depositBalance']
        ];

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
