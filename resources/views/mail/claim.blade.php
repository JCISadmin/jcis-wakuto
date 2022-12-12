@if(is_null($claimName))
{!! $name !!}　御中
@else
{!! $name !!}
{!! $claimName !!} 様
@endif

お世話になっております。
日本信用情報サービスです。

平素より弊社サービスをご利用いただき、誠にありがとうございます。
 
{{ $claimMonth }}月度ご請求書をお送りさせていただきます。
ご不明な点がございましたら、何なりとお問い合わせ下さい。
 
引き続きどうぞよろしくお願い致します。

******************************************************
{{ $companyInfo['name'] }}
@if(!is_null($chargeName))
担当：{!! $chargeName !!}
@endif
住所：〒{{ $companyInfo['postCode'] }}
{{ $companyInfo['address'] }}
TEL：{{ $companyInfo['tel'] }}
FAX：{{ $companyInfo['fax'] }}
HP：https://www.jcis.co.jp/
******************************************************














