<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use ValueError;

/**
 * Class AcurisSearch
 *
 *
 * @package App\Models
 */
class AcurisSearch extends BaseModel
{

    // 法人検索 除外検索条件
    const EXCLUDE_SEARCH_COND_BUSINESSES = [
        'DD'
    ];

    private $contentTypePdf = 'application/pdf';

    private $stubBusinessesResponse =
    '{
        "results": {
            "matchCount": 2,
            "matches": [
                {
                    "qrCode": "432523",
                    "version": 15346444345,
                    "resourceUri": "/businesses/f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b93d8",
                    "resourceId": "f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b93d8",
                    "score": 99,
                    "match": "会社",
                    "name": "会社",
                    "countries": [
                        "JP"
                    ],
                    "datasets": [
                        "PEP"
                    ]
                },
                {
                    "qrCode": "432523",
                    "version": 15346444345,
                    "resourceUri": "/businesses/f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b9123",
                    "resourceId": "f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b9123",
                    "score": 100,
                    "match": "Corporation",
                    "name": "Corporation",
                    "countries": [
                        "US"
                    ],
                    "datasets": [
                        "PEP",
                        "SAN-CURRENT",
                        "SAN-FORMER",
                        "REL",
                        "DD",
                        "INS",
                        "RRE"
                    ]
                }
            ]
        }
    }';

    private $stubIndividualsResponse =
    '{
        "results": {
            "matchCount": 2,
            "matches": [
                {
                    "qrCode": "432523",
                    "version": 15346444345,
                    "resourceUri": "/individuals/1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76cc4",
                    "resourceId": "1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76cc4",
                    "score": 99,
                    "match": "個人",
                    "name": "個人",
                    "countries": [
                        "JP"
                    ],
                    "datesOfBirth": [
                        "1950",
                        "1950-12-31"
                    ],
                    "gender": "Male",
                    "profileImage": "https://www.acurisriskintelligence.com/cdn/content/0024300000/0024297990.jpg",
                    "datasets": [
                        "PEP"
                    ]
                },
                {
                    "qrCode": "123456",
                    "version": 15346444345,
                    "resourceUri": "/individuals/1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76123",
                    "resourceId": "1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76123",
                    "score": 100,
                    "match": "Person",
                    "name": "Person",
                    "countries": [
                        "US"
                    ],
                    "datesOfBirth": [
                        "2000",
                        "2000-01-01"
                    ],
                    "gender": "Female",
                    "profileImage": "https://www.acurisriskintelligence.com/cdn/content/0024300000/0024297990.jpg",
                    "datasets": [
                        "PEP",
                        "SAN-CURRENT",
                        "SAN-FORMER",
                        "REL",
                        "DD",
                        "INS",
                        "RRE"
                    ]
                }
            ]
        }
    }';

    private $uri;
    private $searchCond;
    private $validationRule;
    private $resourceId;
    private $savePath;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        Http::fake([
            // 法人検索
            'http://dev.api.acuris.com/compliance/businesses' => Http::response($this->stubBusinessesResponse, 200, ['Headers']),

            // 個人検索
            'http://dev.api.acuris.com/compliance/individuals' => Http::response($this->stubIndividualsResponse, 200, ['Headers']),

            // 法人詳細検索
            'http://dev.api.acuris.com/compliance/businesses?*' => Http::response(null, 200, ['content-type' => $this->contentTypePdf]),

            // 個人詳細検索
            'http://dev.api.acuris.com/compliance/individuals?*' => Http::response(null, 200, ['content-type' => $this->contentTypePdf]),

        ]);
    }

    /**
     * 法人検索 (Businesses)
     *
     * @param $name
     * @param $datasets
     * @param $countries
     * @return array|bool
     */
    public function businessesSearch($name, $datasets, $countries): array|bool
    {
        // 検索パラメータ設定
        $this->setBusinessesSearchParameter($name, $datasets, $countries);

        // API通信
        $response = $this->acurisSearchAPI();

        // APIで例外エラーが発生した場合
        if($response === FALSE){
            return FALSE;
        }

        // 検索結果を取得
        $ret = $this->getResponseSearchData($response);

        return $ret;
    }

    /**
     * 個人検索 (Individuals)
     *
     * @param $name
     * @param $datasets
     * @param $countries
     * @param $dob
     * @return array|bool
     */
    public function individualsSearch($name, $datasets, $countries, $dob): array|bool
    {
        // 検索パラメータ設定
        $this->setIndividualsSearchParameter($name, $datasets, $countries, $dob);

        // API通信
        $response = $this->acurisSearchAPI();

        // APIで例外エラーが発生した場合
        if($response === FALSE){
            return FALSE;
        }

        // 検索結果を取得
        $ret = $this->getResponseSearchData($response);

        return $ret;
    }

    /**
     * Acuris検索API実行
     *
     * @return Response|bool
     */
    private function acurisSearchAPI(): Response|bool
    {
        // APIキーの取得
        $apiKey = config('acuris.apiKey');

        // APIリクエスト実行 ログ
        Log::info(sprintf("acurisSearchAPI URI:%s apiKey:%s \nparam:\n %s", $this->uri, $apiKey, print_r($this->searchCond, true)));

        try {
            $response = Http::timeout(10)
            ->withHeaders([
                'x-api-key' => $apiKey
            ])->post($this->uri, $this->searchCond);

            return $response;

        } catch (Exception $e) {
            // 例外エラー ログ
            Log::error(sprintf("acurisSearchAPI ExceptionError:\n%s",$e->getTraceAsString()));
            return FALSE;
        }
    }

    /**
     * レスポンスから検索結果を取得
     *
     * @param $response
     * @return array|bool
     */
    private function getResponseSearchData($response): array|bool
    {
        try{
            // ステータスコードが200か判定
            if($response->ok()){

                // 結果取得
                $resultJson = $response->body();
                $result = json_decode($resultJson,true);

                if(is_null($result)){
                    // jsonデコードエラー ログ
                    Log::error(sprintf("acurisSearchAPI JsonDecodeError:getResponseSearchData() status=200 result\n%s",$resultJson));

                    return FALSE;
                }

                // バリデーション
                $validator = Validator::make($result, $this->validationRule);

                // レスポンスデータでバリデーションエラー
                if ($validator->fails()) {
                    // バリデーションエラー ログ
                    Log::error(sprintf('acurisSearchAPI ValidationError:%s',($validator->errors())));
                    return FALSE;
                }

                $retList = [];
                // 検索結果有り
                if($result['results']['matchCount'] > 0){
                    foreach($result['results']['matches'] as $resultItem){
                        $retList[] = $resultItem;
                    }
                }
                $ret = $retList;

            }else{
                // 結果取得
                $resultJson = $response->body();
                $result = json_decode($resultJson,true);

                if(is_null($result)){
                    // jsonデコードエラー ログ
                    Log::error(sprintf("acurisSearchAPI JsonDecodeError:getResponseSearchData() result\n%s",$resultJson));
                    return FALSE;
                }

                // ステータスエラー ログ
                Log::error(sprintf('acurisSearchAPI ResponseStatusError:%s %s', $result['message'], $result['errorDetails']));
                $ret = FALSE;
            }

        }catch(ValueError $e){
            // 例外エラー ログ
            Log::error(sprintf("acurisSearchAPI ValueError:\n%s",$e->getTraceAsString()));
            return FALSE;
        }

        return $ret;
    }

    /**
     * 法人詳細検索 (Businesses)
     *
     * @param $resourceId
     * @param $path
     * @return bool
     */
    public function businesssesLookup($resourceId, $path): bool
    {
        // 検索パラメータ設定
        $this->setBusinessesLookupParameter($resourceId, $path);

        // API通信
        $response = $this->acurisLookupAPI();

        // APIで例外エラーが発生した場合
        if($response === FALSE){
            return FALSE;
        }

        // 検索結果を確認
        $rtn = $this->checkResponseLookupData($response);

        return $rtn;
    }

    /**
     * 個人詳細検索(Individuals)
     *
     * @param $resourceId
     * @param $path
     * @return bool
     */
    public function individualsLookup($resourceId, $path): bool
    {
        // 検索パラメータ設定
        $this->setIndividualsLookupParameter($resourceId, $path);

        // API通信
        $response = $this->acurisLookupAPI();

        // APIで例外エラーが発生した場合
        if($response === FALSE){
            return FALSE;
        }

        // 検索結果を確認
        $rtn = $this->checkResponseLookupData($response);

        return $rtn;
    }

    /**
     * Acuris詳細検索API実行
     *
     * @return Response|bool
     */
    private function acurisLookupAPI(): Response|bool
    {
        // APIキーの取得
        $apiKey = config('acuris.apiKey');

        // APIリクエスト実行 ログ
        Log::info(sprintf('acurisLookupAPI URI:%s apiKey:%s resourceId:%s', $this->uri, $apiKey, $this->resourceId));

        try {
            $response = Http::sink($this->savePath)->timeout(10)
            ->withHeaders([
                'Accept' => $this->contentTypePdf,
                'x-api-key' => $apiKey
            ])->get($this->uri);

            return $response;

        } catch (Exception $e) {
            // 例外エラー ログ
            Log::error(sprintf("acurisLookupAPI ExceptionError:\n%s",$e->getTraceAsString()));
            return FALSE;
        }
    }

    /**
     * レスポンスから詳細検索結果を確認
     *
     * @param $response
     * @return bool
     */
    private function checkResponseLookupData($response): bool
    {
        try{
            // ステータスコードが200か判定
            if($response->ok()){
                $rtn = TRUE;

            }else{
                // 結果取得
                $resultJson = $response->body();
                $result = json_decode($resultJson,true);

                if(is_null($result)){
                    // jsonデコードエラー ログ
                    Log::error('acurisSearchAPI JsonDecodeError:checkResponseLookupData()');
                    return FALSE;
                }

                // ステータスエラー ログ
                Log::error(sprintf('acurisLookupAPI ResponseStatusError:%s', $result['message']));
                $rtn = FALSE;
            }

        }catch(ValueError $e){
            // 例外エラー ログ
            Log::error(sprintf("acurisLookupAPI ValueError:\n%s",$e->getTraceAsString()));
            return FALSE;
        }

        return $rtn;
    }

    /**
     * 検索パラメータ設定(法人検索)
     *
     * @param $name
     * @param $datasets
     * @param $countries
     * @return void
     */
    private function setBusinessesSearchParameter($name, $datasets, $countries): void
    {
        // API 実行URI
        $this->uri = config('acuris.uri.businesses');

        // 個人用検索条件を除外
        $datasets = array_diff($datasets, SELF::EXCLUDE_SEARCH_COND_BUSINESSES);
        $datasets = array_values($datasets);

        // 検索条件
        $this->searchCond = [
            'name' => $name,
            'threshold' => config('acuris.default.threshold'),
            'datasets' => $datasets,
        ];

        if(!is_null($countries)){
            // Acuris側でcountries array型指定のため
            $this->searchCond['countries'] = [$countries];
            $this->searchCond['countryRequired'] = true;
        }

        // Responseバリデーションルール
        $this->validationRule = [
            'results.matchCount' => 'required|integer',
            'results.matches' => 'array',
            'results.matches.*.qrCode' => 'string',
            'results.matches.*.version' => 'integer',
            'results.matches.*.resourceUri' => 'string',
            'results.matches.*.resourceId' => 'required|string',
            'results.matches.*.score' => 'required|integer',
            'results.matches.*.match' => 'required|string',
            'results.matches.*.name' => 'required|string',
            'results.matches.*.countries' => 'array',
            'results.matches.*.datasets' => 'required|array',
        ];
    }

    /**
     * 検索パラメータ設定(個人検索)
     *
     * @param $name
     * @param $datasets
     * @param $countries
     * @param $dob
     * @return void
     */
    private function setIndividualsSearchParameter($name, $datasets, $countries, $dob): void
    {
        // API 実行URI
        $this->uri = config('acuris.uri.individuals');

        // 検索条件
        $this->searchCond = [
            'name' => $name,
            'datasets' => $datasets,
            'threshold' => config('acuris.default.threshold'),
        ];

        if(!is_null($countries)){
            // Acuris側でcountries array型指定のため
            $this->searchCond['countries'] = [$countries];
            $this->searchCond['countryRequired'] = true;
        }

        if (!is_null($dob)) {
            $this->searchCond['dob'] = $dob;
            $this->searchCond['dobMatching'] = config('acuris.default.dobMatching');
            $this->searchCond['dobRequired'] = true;
        }

        // Responseバリデーションルール
        $this->validationRule = [
            'results.matchCount' => 'required|integer',
            'results.matches' => 'array',
            'results.matches.*.qrCode' => 'string',
            'results.matches.*.version' => 'integer',
            'results.matches.*.resourceUri' => 'string',
            'results.matches.*.resourceId' => 'required|string',
            'results.matches.*.score' => 'required|integer',
            'results.matches.*.match' => 'required|string',
            'results.matches.*.name' => 'required|string',
            'results.matches.*.countries' => 'array',
            'results.matches.*.datesOfBirth' => 'array',
            'results.matches.*.gender' => 'string',
            'results.matches.*.profileImage' => 'string',
            'results.matches.*.datasets' => 'required|array',
        ];
    }

    /**
     * 検索パラメータ設定(法人詳細検索)
     *
     * @param $resourceId
     * @param $path
     * @return void
     */
    private function setBusinessesLookupParameter($resourceId, $path): void
    {
        // API 実行URI
        $this->uri = config('acuris.uri.businesses').'/'.$resourceId;

        // パラメータ
        $this->resourceId = $resourceId;

        // PDF保存先パス
        $this->savePath = $path;
    }


    /**
     * 検索パラメータ設定(個人詳細検索)
     *
     * @param $resourceId
     * @param $path
     * @return void
     */
    private function setIndividualsLookupParameter($resourceId, $path): void
    {
        // API 実行URI
        $this->uri = config('acuris.uri.individuals').'/'.$resourceId;

        // パラメータ
        $this->resourceId = $resourceId;

        // PDF保存先パス
        $this->savePath = $path;
    }

}