{!! $companyName !!}
{!! $userName !!}様

添付ファイルパスワードのお知らせ
先程送付しました添付ファイルのパスワードをお知らせします。

TITLE：{{ $zipName }}
PASSWORD：{{ $zipPassword }}

******************************************************
{{ $companyInfo['name'] }}
住所：〒{{ $companyInfo['postCode'] }}
{{ $companyInfo['address'] }}
TEL：{{ $companyInfo['tel'] }}
@if (!empty($companyInfo['fax']))
FAX：{{ $companyInfo['fax'] }}
@endif
HP：{{ $companyInfo['homePageUrl'] }}
******************************************************
