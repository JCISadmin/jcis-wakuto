<style>
    /*td用-タイトル*/
    td.title{
        border: none;
        text-align: center;
        width: 540px;
        height: 40px;
        line-height: 40px;
        font-size: 20px;
    }

    td.sub{
        border: none;
        width: 540px;
        height: 20px;
        line-height: 20px;
        font-size: 8px;
    }

    /*td用--テキスト*/
    td.text{
        border: 0.5px solid black;
        text-align: right;
        height: 30px;
        line-height: 30px;
        font-size: 15px;
    }

    /*td用--項目名*/
    td.column{
        border: 0.5px solid black;
        text-align: left;
        height: 30px;
        line-height: 30px;
        font-size: 15px;
        background-color: #f5f5f5;
    }

    td.column2{
        border: 0.5px solid black;
        text-align: center;
        height: 20px;
        line-height: 20px;
        font-size: 8px;
        background-color: #f5f5f5;
    }

    td.content{
        height: 20px;
        line-height: 20px;
        font-size: 8px;
    }

    table.table_title{
        border: 2px solid black;
    }

    table.table_detail{
        border: 0.5px solid black;
    }

</style>

<table style="border: 2px solid black;" >
        <tr>
            <td class="title">
                利用明細
            </td>
        </tr>
</table>

<table>
        <tr>
            <td class="sub">
                会社名：　{{$companyName}}
            </td>
        </tr>
</table>

<table style="border: none;">
    <tr>
        <td width="270px" class="column">
            発行日時
        </td>
        <td width="270px" class="text">
            {{$date}}
        </td>
    </tr>
    <tr>
        <td width="270px" class="column">
            今月検索件数
        </td>
        <td width="270px" class="text">
            {{number_format($monthSearchCount)}}件
        </td>
    </tr>
    <tr>
        <td width="270px" class="column">
            年間検索件数
        </td>
        <td width="270px" class="text">
            {{number_format($yearSearchCount)}}件
        </td>
    </tr>
</table>

<div hight="10px">
</div>

<table class="table_detail">
    <tr>
        <td class="column2" width="50px">請求年月</td>
        <td class="column2" width="170px">ID/担当者名</td>
        <td class="column2" width="80px">単価</td>
        <td class="column2" width="80px">検索数</td>
        <td class="column2" width="80px">金額</td>
        <td class="column2" width="80px">同一ワード検索数</td>
    </tr>
</table>

@foreach($detail['year'] as $year => $yearItem)
    @if($dispType === 'all')
    <table class="table_detail">
        <tr>
            <td class="content" width="50px">{{ $year }}年</td>
            <td class="content" width="170px"></td>
            <td class="content" width="80px" style="border: none;"></td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$yearItem['totalSearchCount']}}件</td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$yearItem['totalSearchPrice']}}円</td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$yearItem['totalDupSearchCount']}}件</td>
        </tr>
    </table>
    @endif
    @foreach ($detail['month'][$year] as $month => $monthItem)
        <table class="table_detail">
            <tr>
                <td class="content" width="50px" style="border: none;">{{date_format(new DateTime($month), 'Y年n月')}}</td>
                <td class="content" width="170px" style="border: none;"></td>
                <td class="content" width="80px" style="border: none;"></td>
                <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$monthItem['totalSearchCount']}}件</td>
                <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$monthItem['totalSearchPrice']}}円</td>
                <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$monthItem['totalDupSearchCount']}}件</td>
            </tr>   

            @foreach($monthItem['report'] as $userItem)
                @php
                    /* @var  $item */
                @endphp
                <tr>
                    <td class="content" width="50px" style="border: none"></td>
                    <td class="content" width="170px" style="border: 0.5px solid black;">{{$userItem['user']}}</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$userItem['unitPrice']}}円</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$userItem['count']}}件</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$userItem['price']}}円</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$userItem['dupCount']}}件</td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endforeach










<div>
    ※算出件数はVer.3リリース後の件数になります。<br>
    &emsp;システム切り替え以前の件数は含んでおりませんのでご注意ください。
</div>