<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;


/**
 * Class AcurisSearch
 *
 *
 * @package App\Models
 */
class AcurisSearch extends BaseModel
{

    private $businessesSearchURI = 'https://api.acuris.com/compliance/businesses';
    private $individualsSearchURI = 'https://api.acuris.com/compliance/individuals';

    private $contentTypeJson = 'application/json';
    private $contentTypePdf = 'application/pdf';
    
    private $_DEBUG = 1;
    private $stubResponseBusinesses = [
        "results" => [
            "matchCount" => 2,
            "matches" => [
                [
                    "qrCode" => "432523",
                    "version" => 15346444345,
                    "resourceUri" => "/businesses/f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b93d8",
                    "resourceId" => "f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b93d8",
                    "score" => 99,
                    "match" => "IBM Corp",
                    "name" => "IBM Corporation",
                    "countries" => [
                        "US"
                    ],
                    "datasets" => [
                        "SAN-CURRENT"
                    ]
                ],
                [
                    "qrCode" => "11111",
                    "version" => 1111111,
                    "resourceUri" => "/businesses/f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b9111",
                    "resourceId" => "f48f946857281571f7254d8fa51a7f9da0b75e9728c5ab16acace934c08b9111",
                    "score" => 50,
                    "match" => "111 Corp",
                    "name" => "111 Corporation",
                    "countries" => [
                        "US"
                    ],
                    "datasets" => [
                        "PEP"
                    ]
                ],
            ]
        ]
    ];

    private $stubResponseIndividuals = [
        "results" => [
            "matchCount" => 2,
            "matches" => [
                [
                    "qrCode" => "432523",
                    "version" => 15346444345,
                    "resourceUri" => "/individuals/1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76cc4",
                    "resourceId" => "1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76cc4",
                    "score" => 99,
                    "match" => "Boyko Borissov",
                    "name" => "Boyko Metodiev Borisov",
                    "countries" => [
                        "US"
                    ],
                    "datesOfBirth" => [
                        "1959",
                        "1959-08-22"
                    ],
                    "gender" => "Male",
                    "profileImage" => "https://www.acurisriskintelligence.com/cdn/content/0024300000/0024297990.jpg",
                    "datasets" => [
                        "DD",
                        "INS",
                        "PEP-CURRENT",
                        "PEP-FORMER",
                        "PEP-LINKED",
                        "POI",
                        "REL",
                        "RRE",
                        "SAN-CURRENT",
                        "SAN-FORMER",
                        "GRI"
                    ]
                ],

                [
                    "qrCode" => "111111",
                    "version" => 1111111,
                    "resourceUri" => "/individuals/1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76111",
                    "resourceId" => "1f5a940e6a16d390bfe75055c3176f64c5b397880ff08e04b61ad7325af76111",
                    "score" => 50,
                    "match" => "111 Borissov",
                    "name" => "111 Metodiev Borisov AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABBB",
                    "countries" => [
                        "US",
                        "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABBB"

                    ],
                    "datesOfBirth" => [
                        "1959",
                        "1959-08-22",
                        "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABBB"
                    ],
                    "gender" => "Male",
                    "profileImage" => "https://www.acurisriskintelligence.com/cdn/content/0024300000/0024297990.jpg",
                    "datasets" => [
                        "DD",
                        "INS",
                        "PEP-CURRENT",
                        "PEP-FORMER",
                        "PEP-LINKED",
                        "POI",
                        "REL",
                        "RRE",
                        "SAN-CURRENT",
                        "SAN-FORMER",
                        "GRI"
                    ]
                ],
            ]
        ]
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        if($this->_DEBUG === 1){
            
            // 法人検索
            Http::fake([
                $this->businessesSearchURI => Http::response($this->stubResponseBusinesses, 200, ['Headers'])
            ]);

            // 個人検索
            Http::fake([
                $this->individualsSearchURI => Http::response($this->stubResponseIndividuals, 200, ['Headers'])
            ]);

            // 法人詳細検索
            Http::fake([
                $this->businessesSearchURI.'?*' => Http::response(null, 200, ['content-type' => $this->contentTypePdf])
            ]);

            // 個人詳細検索
            Http::fake([
                $this->individualsSearchURI.'?*' => Http::response(null, 200, ['content-type' => $this->contentTypePdf])
            ]);
        }
    }


    /**
     * 法人検索(API)
     *
     * @param $name
     * @param $datasets
     * @param $countries
     * @return
     */
    public function businessesSearch($name, $datasets, $countries)
    {

        try {
            // 一覧検索(法人)
            $response = Http::timeout(10)
            ->withHeaders([
                'x-api-key' => config('hds.acuris.apiKey')
            ])->post($this->businessesSearchURI, [
                'name' => $name,
                'threshold' => 50,
                'countries' => $countries,
                'datasets' => $datasets,
            ]);

        } catch (ConnectionException $e) {

            dump($e->getMessage());
            throw $e;

        }

        return $response;
    }

    /**
     * 個人検索(API)
     *
     * @param $name
     * @param $datasets
     * @param $countries
     * @param $dob
     * @return
     */
    public function individualsSearch($name, $datasets, $countries, $dob)
    {

        try {
            // 一覧検索(個人)
            $response = Http::timeout(10)
            ->withHeaders([
                'x-api-key' => config('hds.acuris.apiKey')
            ])->post($this->individualsSearchURI, [
                'name' => $name,
                'datasets' => $datasets,
                'threshold' => 50,
                'countries' => $countries,
                'dob' => $dob,
                'dobMatching' => 'exact',
                'gender' => NULL,
                'dobRequired' => !is_null($dob),
                'countryRequired' => !is_null($countries),
            ]);

        } catch (ConnectionException $e) {

            dump($e->getMessage());
            throw $e;

        }

        return $response;
    }


    /**
     * 法人詳細検索(API)
     *
     * @param $resourceId
     * @param $path
     * @return
     */
    public function businessesLookup($resourceId, $path)
    {

        try {
            // 詳細検索(法人)
            $response = Http::sink($path)->timeout(10)
            ->withHeaders([
                'Accept' => $this->contentTypePdf,
                'x-api-key' => config('hds.acuris.apiKey')
            ])->get($this->businessesSearchURI, [
                'resourceId' => $resourceId
            ]);

        } catch (ConnectionException $e) {

            dump($e->getMessage());
            throw $e;

        }

        return $response;
    }

    /**
     * 個人詳細検索(API)
     *
     * @param $resourceId
     * @return
     */
    public function individualsLookup($resourceId, $path)
    {

        try {
            // 詳細検索(法人)
            $response = Http::sink($path)->timeout(10)
            ->withHeaders([
                'Accept' => $this->contentTypePdf,
                'x-api-key' => config('hds.acuris.apiKey')
            ])->get($this->individualsSearchURI, [
                'resourceId' => $resourceId
            ]);

        } catch (ConnectionException $e) {

            dump($e->getMessage());
            throw $e;

        }

        return $response;
    }



}