<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\MUserDetail;
use App\Models\SearchEngine;

/**
 * 検索用API
 */
class SearchController extends Controller
{

    /**
     * APIプランを認証し、DBの検索を行う
     *
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function authSearch(Request $request): string
    {
        $authModel = new MUserDetail();
        $searchModel = new SearchEngine();

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
                "query" => NULL,
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
        }


        // 検索を行う
        $response = [ "query" => [] ];
        $queries = $request['query'];

        if (!is_array($queries)) {
            $response = [
                "status" => "NG",
                "code" => "e001",
                "query" => NULL,
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
        }

        foreach ($queries as $query) {

            // バリデーションを行う
            // 必須項目の確認
            if (!isset($query['type'])) {
                $errorCode = "e004";
            } else if (!isset($query['keyword'])) {
                $errorCode = "e005";
            }
            if($errorCode !== "") {
                $response = [
                    "status" => "NG",
                    "code" => $errorCode,
                    "query" => NULL,
                ];
                return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
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
                $birthday = "";
            }

            // バリデーションを行う
            // 要素のフォーマット整合
            if($errorCode !== "") {
                $response = [
                    "status" => "NG",
                    "code" => $errorCode,
                    "query" => NULL,
                ];
                return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
            }


            // 検索箇所ごとにメソッド呼び出し
            $result = [];
            if ($query['type'] === "person") {
                $result = $searchModel->searchPerson($authData->companyId, $authData->contractPlanId, $authData->userId, $keyword, '', '', false, $birthday);
            } else if ($query['type'] === "company") {
                $result = $searchModel->searchCompany($authData->companyId, $authData->contractPlanId, $authData->userId, $keyword, '', false);
            }

            //検索結果から開示不要な項目を削除
            $removeAry = [
                'personId' => '',
                'corporationId' => '',
                'inputName' => '',
                'inputKana' => '',
                'infoKind' => '',
                'infoSource' => '',
                'filename' => '',
                'regDate' => '',
                'note' => '',
                'createDatetime' => '',
                'updateDatetime' => '',
                'formatBirthday' => '',
                'formatCaseDate' => ''
            ];

            if($result !== []){
                foreach($result as $key => $list){
                    $result[$key] = array_diff_key($list, $removeAry);
                }
            }

            $tmpRequest = [
                "type" => $query['type'],
                "keyword" => $keyword,
                "birthday" => $birthday,
                "result" => $result,
            ];
            array_push($response['query'], $tmpRequest);
        }
        $response["status"] = "OK";
        $response["code"] = "i001";

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
