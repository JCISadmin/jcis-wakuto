<?php

return [
    'app' => [
        'version' => '1.00',
        'loginNote' => '注意書き'
    ],

    'contact' => [
        'subject' => [
            '1' =>'担当者名の変更',
            '2' =>'運用部署の変更（組織改編等)',
            '3' =>'同一性確認の相談等',
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
        'trialPlan'=>[
            'web'=>'4',
            'api'=>'',
        ],
    ],

];
