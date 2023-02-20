<?php

return [
    'contact' => [
        'subject' => [
            '1' =>'操作に関するお問い合わせ',
            '2' =>'出力情報に関するお問い合わせ',
            '3' =>'システムトラブルに関するお問い合わせ',
            '4' =>'その他'
        ],

        'mailSubject' => 'お問い合わせ',
        'to' => env('CONTACT_TO', 'contact@entrend.net'),
    ],

    'auth' => [
        'loginInterval' => 'PT1H', // DateInterval表記
        '2factExpireInterval' => 'PT30M',

        'mailSubject' => '認証コード',
    ],

    'contract' => [
        'trialPlan' => [
            'web' => 'trial',
            'api' => '',
        ],
    ],

    'claim' => [
        'imageFileName' => [
            'companyName' => 'company_logo.jpg',
            'companyStamp' => 'company_stamp.jpg',
        ],

        'mailSubject' => '月度ご請求書のご案内', // 文字列頭に請求月の変数が挿入
    ],

    'subject' => [
        'web' => [
            'trial' => 'Jcisチェックシステム(トライアル)',
            'regular' =>'Jcisチェックシステム',
        ],
        'api' => [
            'trial' =>'反社データベースAPI即時チェックシステム(トライアル)',
            'regular' =>'反社データベースAPI即時チェックシステム',
        ],
        'acuris' => [
            'trial' =>'',
            'regular' =>'Acuris検索',
        ],
    ],

    'title' => 'JCIS WEBDB Ver3',

    'url' => [
        'web' => "https://www.jcisdb-v3.com",
        'api' => [
            'search' => "https://www.jcisdb-v3.com/api/search",
            'useReport' => "https://www.jcisdb-v3.com/api/useReport",
        ],
        'header' => [
            'manual' => "https://jcisdb-v3.s3.ap-northeast-1.amazonaws.com/jcisdb-v3_usermanual.pdf",
        ],
        'footer' => [
            'companyInfo' => "https://jcis.co.jp/",
            'privacy' => "https://jcis.co.jp/privacy/",
            'terms' => "https://jcisdb-v3.s3.ap-northeast-1.amazonaws.com/jcisdb-v3_terms.pdf",
        ],
    ],

    'bulkSearch' => [
        'maxDispNum' => [
            'pdfFromCsv' => 500,
        ]
    ],

    'registryInfo' => [
        //他の文字と部分一致する場合、文字数の多い方を上部に定義してください。
        'position' => [
            'representative' => [
                '代表取締役',
                '代表理事',
                '理事長',
                '代表社員',
                '代表役員',
                '代表執行役',
                '執行役員',
            ],
            'normal' => [
                '取締役・監査等',
                '仮取締役',
                '取締役',
                '監査役',
                '理事',
                '監事',
                '評議員',
                '会計参与',
                '参事',
                '監督役員',
                '報酬委員',
                '執行役',
                '監査委員',
                '業務執行社員',
                '指名委員',
                '社員',
            ],
            'exclusion' => [
                '監査役の監査の範囲',
                '会計監査人'
            ],
        ],
        'retire' => [
            '退任',
            '辞任',
            '死亡',
            '解任'
        ],
        'appoint' => [
            '就任',
            '重任'
        ],
        'replaceSymbol' => [
            'verticalLine' => [
                '┃'
            ],
            'horizonLine' => [
                '－','━'
            ],
            'other' => ['┌','┐','┘','└','├','┬','┤','┴','┼','┏','┓','┛','┗','┣','┳','┫','┻','╋','┿','┷','┯','┠','┨'],
        ],
        'removeSymbol' => [
            '　',' ',PHP_EOL,"\f"
        ],
    ],
    'user' => [
        'paymentTerm' => [
            '1' => [
                'name' => '翌月10日',
                'modify' => 'next month 9 day',
            ],
            '2' => [
                'name' => '翌月15日',
                'modify' => 'next month 14 day',
            ],
            '3' => [
                'name' => '翌月20日',
                'modify' => 'next month 19 day',
            ],
            '4' => [
                'name' => '翌月25日',
                'modify' => 'next month 24 day',
            ],
            '5' => [
                'name' => '翌月末日',
                'modify' => 'last day of next month',
            ],
            '6' => [
                'name' => '翌々月5日',
                'modify' => 'next month next month 4 day',
            ],
            '7' => [
                'name' => '翌々月末日',
                'modify' => 'next month last day of next month',
            ],
        ]
    ],
    'acuris' => [
        'search' => [
            'normal' => [
                'title' => 'Acuris一覧検索',
                'unitPrice' => 500,
            ],
            'detail' => [
                'title' => 'Acuris詳細検索',
                'unitPrice' => 1500,
            ],
        ],
    ],
    'keywordHistory' => [
        'freePeriod' => '+365 days',
    ]

];
