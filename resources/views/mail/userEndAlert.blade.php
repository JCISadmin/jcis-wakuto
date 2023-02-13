{!! $companyName !!}
{!! $staffDepartmentJob !!}
{!! $staffName !!} 様

いつも大変お世話になっております。
日本信用情報サービスの{!! $chargeName !!}です。
当社の反社チェックサービスをご利用頂きましてありがとうございます。

契約更新のご案内のメールをさせて頂きました。
{!! $useEndDate->format('Y年m月d日') !!}が利用終了日となりますがいかがでしょうか。
ご継続の場合は、自動更新となりますので手続き等は当社側では必要はございません。

現在ご契約状況
利用開始日:{!! $useStartDate->format('Y/m/d') !!}
次回利用更新日:{!! $useUpdateDate->format('Y/m/d') !!}
利用終了日:{!! $useEndDate->format('Y/m/d') !!}
ID代:{!! $idUnitPrice !!}円(月)
検索単価:{!! $searchUnitPrice !!}円

更新についてご不明な点等ございましたらご連絡ください。
担当者：{!! $chargeName !!}
メールアドレス：{!! $chargeMail !!}
電話番号：●●

引き続きよろしくお願いいたします。
※本メールはシステムからの自動送信になります。