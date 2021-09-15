<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MUserDetail;
use App\Models\SearchEngine;

use function PHPUnit\Framework\isEmpty;

/**
 * 検索用API
 */
class SearchController extends Controller
{

    /**
     * APIプランを認証し、DBの検索を行う
     * 
     * @param Request $request
     * @return Json $responseJson
     */
    public function authSearch(Request $request) {
        $authModel = new MUserDetail();
        $searchModel = new SearchEngine();

        $response = array();

        // バリデーションを行う
        // 必須項目の確認
        $errorCode = "";
        if (isEmpty($request->all())) {
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
            $responseJson = json_encode($response, JSON_UNESCAPED_UNICODE);
            return mb_convert_encoding($responseJson, "UTF-8");
        }


        // 認証を行う
        $authData = $authModel->getUserCredentialsApi($request['id'], $request['password']);
        if ($authData === NULL) {
            $response = [
                "status" => "NG",
                "code" => "w001",
                "query" => NULL,
            ];
            $responseJson = json_encode($response, JSON_UNESCAPED_UNICODE);
            return mb_convert_encoding($responseJson, "UTF-8");
        }


        // 検索を行う
        $response = [ "query" => [] ];
        $querys = $request['query'];

        foreach ($querys as $query) {

            // バリデーションを行う
            // 必須項目の確認
            if (!isset($query['type'])) {
                $errorCode = "e004";
            } else if (!isset($query['keyword'])) {
                $errorCode = "e005";
            }

            // 検索キーワードのフィルターを適用
            $keyword = "";
            if ($query['type'] === "person") {
                $keyword = $searchModel->filterPerson($query['keyword']);
            } else if ($query['type'] === "company") {
                $keyword = $searchModel->filterCompany($query['keyword']);
            }
            if ($keyword === "") {
                $errorCode = "e006";
            }

            // 生年月日のフォーマットを確認
            if (isset($query['birthday'])) {
                $birthday = $query['birthday'];
                if (!strptime($birthday, '%Y-%m-%d')) {
                    $errorCode = "e007";
                }
            } else {
                $birthday = NULL;
            }

            if($errorCode !== "") {
                $response = [
                    "status" => "NG",
                    "code" => $errorCode,
                    "query" => NULL,
                ];
                $responseJson = json_encode($response, JSON_UNESCAPED_UNICODE);
                return mb_convert_encoding($responseJson, "UTF-8");
            }


            // 検索箇所ごとにメソッド呼び出し
            if ($query['type'] === "person") {
                $result = $searchModel->searchPerson($authData->companyId, $authData->contractPlanId, $authData->userId, $keyword, '', '', false, $query['birthday']);
            } else if ($query['type'] === "company") {
                $result = $searchModel->searchCompany($authData->companyId, $authData->contractPlanId, $authData->userId, $keyword, '', false);
            }

            $tmpRequest = [
                "type" => $query['type'],
                "keyword" => $query['keyword'],
                "birthday" => $birthday,
                "result" => $result,
            ];
            array_push($response['query'], $tmpRequest);
        }
        $response["status"] = "OK";
        $response["code"] = "i001";
        $responseJson = json_encode($response, JSON_UNESCAPED_UNICODE);

        return mb_convert_encoding($responseJson, "UTF-8");
    }
}    