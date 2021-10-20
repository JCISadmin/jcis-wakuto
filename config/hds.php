<?php

return [
    'app' => [
        'version' => '1.00',
        'loginNote' => '・注意事項
        ログインは弊社から通達したユーザーID（jcis-*****-***）とパスワードをそのまま入力してください。どちらも半角です。コピー＆ペーストする際は前後に余計なスペース等を含まないようご注意ください。
        セキュリティの観点から多重ログインを許容しておりません。ログインエラーになった際は、入力誤りだけでなく他の人が使用していないかもご確認ください。
        使用後は必ずログアウトしてください。5分間操作がない場合、自動的にログアウトされます。
        不特定多数の人が同じパソコンを使う環境ではログイン情報を保存しないでください。'
    ],

    'contact' => [
        'subject' => [
            '1' =>'操作に関するお問い合わせ',
            '2' =>'出力情報に関するお問い合わせ',
            '3' =>'システムトラブルに関するお問い合わせ',
            '4' =>'その他'
        ],

        'mailSubject' => 'お問い合わせ',
        'to' => 'contact@entrend.net',
    ],

    'auth' => [
        'loginInterval' => 'PT5H', // DateInterval表記
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
            'companyName' => '会社ロゴ_社名.jpg',
            'companyStamp' => 'JCIS角印印影.jpg',
        ],

        'mailSubject' => '月度ご請求書のご案内', // 文字列頭に請求月の変数が挿入
        'to' => 'claim@entrend.net',
    ],

    'subject' => [
        'web' => [
            'trial' => '反社データベースWEB即時チェックシステム(トライアル)',
            'regular' =>'反社データベースWEB即時チェックシステム',
        ],
        'api' => [
            'trial' =>'反社データベースAPI即時チェックシステム(トライアル)',
            'regular' =>'反社データベースAPI即時チェックシステム',
        ],
    ],

    'title' => '反社DB検索システム',

    'url' => [
        'web' => "https://jcisdb.com/hansha/web",
        'api' => "https://jcisdb.com/hansha/api",
    ],

];
