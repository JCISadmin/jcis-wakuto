<style>
    td {
      height: 15px;
    }
    .result-table {
        border: 2px solid black;
    }
    .resule-column {
        border: 0.5px solid black;
    }
    .tytle-column {
        background-color: #eeeeee;
    }
    .column-1 {
        width: 15%;
    }
    .column-2 {
        width: 24%;
    }
    .column-3 {
        width: 15%;
    }
    .column-4 {
        width: 21%;
    }
    .column-5 {
        width: 12%;
    }
    .column-6 {
        width: 13%;
    }
</style>

<div></div>
<div></div>

<table>
    @if (isset($keyword['company']))
    <tr>
        <td width="100px"></td>
        <td width="360px" class="tytle-text">法人名検索</td>
    </tr>
        @foreach ($keyword['company'] as $isExist => $items)
        @foreach ($items as $item)
        <tr>
            <td width="100px"></td>
            <td width="360px">検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし"; }}</td>
        </tr>
        @endforeach
        @endforeach
    <tr>
        <td  style="height: 10px;"></td>
    </tr>
    @endif
    @if (isset($keyword['person']))
    <tr>
        <td width="100px"></td>
        <td width="360px" class="tytle-text">個人名検索</td>
    </tr>
        @foreach ($keyword['person'] as $isExist => $items)
        @foreach ($items as $item)
        <tr>
            <td width="100px"></td>
            <td width="360px">検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし"; }}</td>
        </tr>
        @endforeach
        @endforeach
    @endif
</table>

<tr>
    <td  style="height: 10px;"></td>
</tr>

@foreach ($result as $item)
<table class="result-table">
        @if ($item['searchType'] === "company")
        <tr>
            <td class="resule-column tytle-column column-1">
            事案年月日
            </td>
            <td class="resule-column column-2">
            {{ $item['formatCaseDate'] }}
            </td>
            <td class="resule-column tytle-column column-3">
            法人・団体名
            </td>
            <td class="resule-column column-4">
            {{ $item['dispName'] }}
            </td>
            <td class="resule-column tytle-column column-5">
            業種
            </td>
            <td class="resule-column column-6">
            {{ $item['industry'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                当時郵便番号
            </td>
            <td class="resule-column column-2">
                {{ $item['postCode'] }}
            </td>
            <td class="resule-column tytle-column column-3" style="line-height: 4px">
                所在地の電話番号
            </td>
            <td class="resule-column column-4">
                {{ $item['tel'] }}
            </td>
            <td class="resule-column tytle-column column-5">
                法人番号
            </td>
            <td class="resule-column column-6">
                {{ $item['corporateCode'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                当時所在地
            </td>
            <td width="60%" class="resule-column">
                {{ $item['address'] }}
            </td>
            <td class="resule-column tytle-column column-5">
                要件区分
            </td>
            <td class="resule-column column-6">
                {{ $item['requireDivision'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                事案概要
            </td>
            <td width="85%" class="resule-column">
                {{ $item['caseSummary'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                当時代表者名
            </td>
            <td class="resule-column column-2">
                {{ $item['delegate'] }}
            </td>
            <td class="resule-column tytle-column column-3">
                事案個人名
            </td>
            <td width="46%" class="resule-column">
                {{ $item['casePersonName'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                当時実質経営者
            </td>
            <td class="resule-column column-2">
                {{ $item['businessOwner'] }}
            </td>
            <td width="36%" class="resule-column">
                {{ $item['department'] }}
            </td>
            <td class="resule-column tytle-column column-5">
                処分官署
            </td>
            <td class="resule-column column-6">
                {{ $item['disposalOffice'] }}
            </td>
        </tr>
        @elseif ($item['searchType'] === "person")

        <tr>
            <td class="resule-column tytle-column column-1">
                事案年月日
            </td>
            <td class="resule-column column-2">
                {{ $item['formatCaseDate'] }}
            </td>
            <td class="resule-column tytle-column column-3">
                氏名
            </td>
            <td class="resule-column column-4">
                {{ $item['dispName'] }}
            </td>
            <td class="resule-column tytle-column column-5">
                異名・かな
            </td>
            <td class="resule-column column-6">
                {{ $item['dispKana'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                現年齢(※1)
            </td>
            <td class="resule-column column-2">
                {{$item['age'] }}
            </td>
            <td class="resule-column tytle-column column-3">
                生年月日
            </td>
            <td class="resule-column column-4">
                {{ $item['formatBirthday'] }}
            </td>
            <td class="resule-column tytle-column column-5">
                要件区分
            </td>
            <td class="resule-column column-6">
                {{ $item['requireDivision'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                当時郵便番号
            </td>
            <td class="resule-column column-2">
                {{ $item['postCode'] }}
            </td>
            <td class="resule-column tytle-column column-3">
                当時所属・役職
            </td>
            <td class="resule-column column-4">
                {{ $item['departmentJob'] }}
            </td>
            <td class="resule-column tytle-column column-5">
                当時官署
            </td>
            <td class="resule-column column-6">
                {{ $item['disposalOffice'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                当時住所
            </td>
            <td width="60%" class="resule-column">
                {{ $item['address'] }}
            </td>
            <td class="resule-column tytle-column column-5">
                当時年齢
            </td>
            <td class="resule-column column-6">
                {{ $item['caseAge'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                事案概要
            </td>
            <td width="85%" class="resule-column">
                {{ $item['caseSummary'] }}
            </td>
        </tr>
        <tr>
            <td class="resule-column tytle-column column-1">
                当時所属団体名
            </td>
            <td class="resule-column column-2">
                {{ $item['department'] }}
            </td>
            <td class="resule-column tytle-column column-3">
                当時団体所在地
            </td>
            <td width="46%" class="resule-column">
                {{ $item['departmentAddress'] }}
            </td>
        </tr>
        @endif
</table>
<tr>
    <td  style="height: 10px;"></td>
</tr>
@endforeach
<p style="text-align:left;">
    •本検索サービスを通じて提供する情報は、独自収集した結果に基づくものであり、絶対的な情報を提供するものではありません。<br>
    •本サービスを通じて提供する情報は、同名であっても同一性を保証するものではありません。<br>
    •本サービスを通じて提供する情報を元に、自己のデータベースを構築することは禁止します。<br>
    {{ $companyInfo['name'] }}<br>
    Japan Credit Information Service Co., Ltd.<br>
    {{ $companyInfo['address'] }}<br>
    TEL: {{ $companyInfo['tel'] }}<br>
    WEB: https://www.jcis.co.jp
</p>
