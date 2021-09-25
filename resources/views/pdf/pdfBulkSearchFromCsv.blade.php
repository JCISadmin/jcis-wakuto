<!--suppress HtmlDeprecatedAttribute, CssNonIntegerLengthInPixels -->
<style>
    /*td用--項目名*/
    td.column{
        border: 0.5px solid black;
        text-align: left;
        height: 20px;
        line-height: 30px;
        font-size: 10px;
        background-color: #f5f5f5;
    }

    /*td用--テキスト*/
    td.text{
        border: 0.5px solid black;
        text-align: left;
        height: 20px;
        line-height: 30px;
        font-size: 10px;
    }

    td.column_b_less {
        border-right: 0.5px solid black;
        border-left: 0.5px solid black;
        text-align: left;
        height: 30px;
        line-height: 30px;
        font-size: 10px;
        background-color: #f5f5f5;
    }
    td.column_t_less {
        border-right: 0.5px solid black;
        border-left: 0.5px solid black;
        border-bottom: 0.5px solid black;
        text-align: left;
        height: 30px;
        line-height: 30px;
        font-size: 10px;
        background-color: #f5f5f5;
    }
    td.text_b_less {
        border-right: 0.5px solid black;
        border-left: 0.5px solid black;
        text-align: right;
        height: 30px;
        line-height: 30px;
        font-size: 10px;
    }
    td.text_t_less {
        border-right: 0.5px solid black;
        border-left: 0.5px solid black;
        border-bottom: 0.5px solid black;
        text-align: right;
        height: 30px;
        line-height: 30px;
        font-size: 10px;
    }
</style>


<table style="border: none;">

    <tr>
        <td width="25%" class="column">
            アップロードファイル名
        </td>
        <td width="70%" class="text">
            {{ $fileName }}
        </td>
    </tr>

    <tr>
        <td width="25%" class="column">
            実行日時
        </td>
        <td width="47%" class="text">
            {{ $executeDate }}
        </td>
        <td width="15%" class="column">
            結果(法+個)
        </td>
        <td width="8%" class="text">
            {{ $isHitSearch ? '○' : '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="column">
            法人
        </td>
        <td width="17%" class="column">
            法人名
        </td>
        <td width="18%" class="column">
            法人番号
        </td>
        <td width="40%" class="column">
            会社住所
        </td>
        <td width="8%" class="column">
            結果
        </td>
    </tr>

@foreach ($searchData['keyword'] as $key => $item)
@if ( !is_null($item['type']) )
@if ( $item['type'] === "法人検索" )
    <tr>
        <td width="12%" class="text">
            {{ isset($item['listIndex']) ? $item['listIndex'] + 1 : '' }}
        </td>
        <td width="17%" class="text">
            {{ $item['name'] ?? '' }}
        </td>
        <td width="18%" class="text">

        </td>
        <td width="40%" class="text" style="line-height: 12px !important;">

        </td>
        <td width="8%" class="text">
            {{ $item['hitSign'] ?? '' }}
        </td>
    </tr>
@endif
@endif
@endforeach

    <tr>
        <td width="12%" class="column">
            個人
        </td>
        <td width="17%" class="column">
            個人名・役員名
        </td>
        <td width="18%" class="column">
            構成員役職名
        </td>
        <td width="40%" class="column">
            代表取締役住所
        </td>
        <td width="8%" class="column">
            結果
        </td>
    </tr>

@foreach ($searchData['keyword'] as $key => $item)
@if ( !is_null($item['type']) )
@if ( $item['type'] === "個人検索" )
    <tr>
        <td width="12%" class="text">
            {{ isset($item['listIndex']) ? $item['listIndex'] + 1 : '' }}
        </td>
        <td width="17%" class="text">
            {{ $item['name'] ?? '' }}
        </td>
        <td width="18%" class="text">

        </td>
        <td width="40%" class="text">

        </td>
        <td width="8%" class="text">
            {{ $item['hitSign'] ?? '' }}
        </td>
    </tr>
@endif
@endif
@endforeach

@if ($isHitCompany)
    <tr>
        <td width="12%" class="column_b_less">
            該当法人
        </td>
        <td width="35%" class="column">
            該当法人名
        </td>
        <td width="12%" class="column">
            業種
        </td>
        <td width="16%" class="column">
            法人番号
        </td>
        <td width="20%" class="column">
            所在地の電話番号
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_b_less">

        </td>
        <td width="17%" class="column">
            当時郵便番号
        </td>
        <td width="66%" class="column">
            当時所在地
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_b_less">

        </td>
        <td width="17%" class="column">
            当時代表者
        </td>
        <td width="18%" class="column">
            当時実質経営者
        </td>
        <td width="48%" class="column">
            当時実質経営者所属
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_b_less">

        </td>
        <td width="17%" class="column">
            事案年月日
        </td>
        <td width="46%" class="column">
            要件区分
        </td>
        <td width="20%" class="column">
            事案個人名
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_t_less">

        </td>
        <td width="63%" class="column">
            事案概要
        </td>
        <td width="20%" class="column">
            処分官署
        </td>
    </tr>
@endif

@foreach ($searchData['corporationList'] as $key => $items)
@if( !empty($item) )
@foreach ($items as $item)
    <tr>
        <td width="12%" class="text_b_less">
            {{ $key + 1 }}.
        </td>
        <td width="35%" class="text">
            {{ $item['dispName'] ?? '' }}
        </td>
        <td width="12%" class="text">
            {{ $item['industry'] ?? '' }}
        </td>
        <td width="16%" class="text">
            {{ $item['corporateCode'] ?? '' }}
        </td>
        <td width="20%" class="text">
            {{ $item['tel'] ?? '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_b_less">
            @if(count($items) > 1)
            (複数該当)
            @endif
        </td>
        <td width="17%" class="text">
            {{ $item['postCode'] ?? '' }}
        </td>
        <td width="66%" class="text">
            {{ $item['address'] ?? '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_b_less">

        </td>
        <td width="17%" class="text">
             {{$item['delegate'] ?? '' }}
        </td>
        <td width="18%" class="text">
            {{ $item['businessOwner'] ?? '' }}
        </td>
        <td width="48%" class="text">
            {{ $item['department'] ?? '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_b_less">

        </td>
        <td width="17%" class="text">
            {{ $item['caseDate'] ?? '' }}
        </td>
        <td width="46%" class="text">
            {{ $item['requireDivision'] ?? '' }}
        </td>
        <td width="20%" class="text">
            {{ $item['casePersonName'] ?? '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_t_less">

        </td>
        <td width="63%" class="text">
            {{ $item['caseSummary'] ?? '' }}
        </td>
        <td width="20%" class="text">
            {{ $item['disposalOffice'] ?? '' }}
        </td>
    </tr>
@endforeach
@endif
@endforeach

@if ($isHitPerson)
    <tr>
        <td width="12%" class="column_b_less">
            該当個人
        </td>
        <td width="35%" class="column">
            該当個人名
        </td>
        <td width="12%" class="column">
            該当異名・かな
        </td>
        <td width="16%" class="column">
            生年月日
        </td>
        <td width="20%" class="column">
            現年齢
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_b_less">

        </td>
        <td width="17%" class="column">
            当時郵便番号
        </td>
        <td width="66%" class="column">
            当時住所
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_b_less">

        </td>
        <td width="17%" class="column">
            当時所属・役職
        </td>
        <td width="18%" class="column">
            当時所属団体名
        </td>
        <td width="48%" class="column">
            当時団体所在地
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_b_less">

        </td>
        <td width="17%" class="column">
            事案年月日
        </td>
        <td width="46%" class="column">
            要件区分
        </td>
        <td width="20%" class="column">
            当時年齢
        </td>
    </tr>

    <tr>
        <td width="12%" class="column_t_less">

        </td>
        <td width="63%" class="column">
            事案概要
        </td>
        <td width="20%" class="column">
            処分官署
        </td>
    </tr>
@endif

@foreach ($searchData['personList'] as $key => $items)
@if( !empty($item) )
@foreach ($items as $item)
    <tr>
        <td width="12%" class="text_b_less">
            {{ $key + 1 }}.
        </td>
        <td width="35%" class="text">
            {{ $item['dispName'] ?? '' }}
        </td>
        <td width="12%" class="text">
            {{ $item['dispKana'] ?? '' }}
        </td>
        <td width="16%" class="text">
            {{ $item['birthday'] ?? '' }}
        </td>
        <td width="20%" class="text">
            {{ $item['age'] }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_b_less">
            @if(count($items) > 1)
            (複数該当)
            @endif
        </td>
        <td width="17%" class="text">
            {{ $item['postCode'] ?? '' }}
        </td>
        <td width="66%" class="text">
            {{ $item['address'] ?? '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_b_less">

        </td>
        <td width="17%" class="text">
             {{$item['departmentJob'] ?? '' }}
        </td>
        <td width="18%" class="text">
            {{ $item['department'] ?? '' }}
        </td>
        <td width="48%" class="text">
            {{ $item['departmentAddress'] ?? '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_b_less">

        </td>
        <td width="17%" class="text">
            {{ $item['caseDate'] ?? '' }}
        </td>
        <td width="46%" class="text">
            {{ $item['requireDivision'] ?? '' }}
        </td>
        <td width="20%" class="text">
            {{ $item['caseAge'] ?? '' }}
        </td>
    </tr>

    <tr>
        <td width="12%" class="text_t_less">

        </td>
        <td width="63%" class="text">
            {{ $item['caseSummary'] ?? '' }}
        </td>
        <td width="20%" class="text">
            {{ $item['disposalOffice'] ?? '' }}
        </td>
    </tr>
@endforeach
@endif
@endforeach

</table>
