<style>
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

    td.column{
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

<table class="table_title" >
        <tr>
            <td class="title">
                月別検索数
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

<table class="table_detail">
    <tr>
        <td class="column" width="50px">請求年月</td>
        <td class="column" width="170px">ID/担当者名</td>
        <td class="column" width="80px">単価</td>
        <td class="column" width="80px">検索数</td>
        <td class="column" width="80px">金額</td>
        <td class="column" width="80px">同一ワード検索数</td>
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
    @foreach ($detail['month'] as $month => $monthItem)
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
                    <td class="content" width="80px" style="text-align: right;"></td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endforeach