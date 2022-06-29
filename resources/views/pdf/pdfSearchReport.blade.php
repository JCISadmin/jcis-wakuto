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
        <td class="column" width="80px">同一ワード検索数</td>
        <td class="column" width="80px">金額</td>
    </tr>
</table>

@foreach($detail as $yearItem)
    @if(isset($yearItem['year']))
    <table class="table_detail">
        <tr>
            <td class="content" width="50px">{{ $yearItem['year'] }}</td>
            <td class="content" width="170px"></td>
            <td class="content" width="80px" style="border: none;"></td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$yearItem['yearTotalCount']}}件</td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$yearItem['yearTotalPrice']}}円</td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">0件</td>
        </tr>

        @foreach($yearItem['userInfo'] as $yearValue)
        <tr>
            <td class="content" width="50px" style="border: none"></td>
            <td class="content" width="170px" style="border-top: 0.5px solid black; border-bottom: 0.5px solid black; border-left: 0.5px solid black;">{{$yearValue['user']}}</td>
            <td class="content" width="80px" style="border-top: 0.5px solid black; border-bottom: 0.5px solid black;"></td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$yearValue['count']}}件</td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$yearValue['price']}}円</td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">0件</td>
        </tr>
        @endforeach
    </table>
    @endif
    @foreach ($yearItem['monthList'] as $item)
        <table class="table_detail">
            <tr>
                <td class="content" width="50px" style="border: none;">{{date_format(new DateTime($item['month']), 'Y/m')}}</td>
                <td class="content" width="170px" style="border: none;"></td>
                <td class="content" width="80px" style="border: none;"></td>
                <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$item['monthTotalCount']}}件</td>
                <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$item['monthTotalPrice']}}円</td>
                <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">0件</td>
            </tr>   

            @foreach($item['userInfo'] as $value)
                @php
                    /* @var  $item */
                @endphp
                <tr>
                    <td class="content" width="50px" style="border: none"></td>
                    <td class="content" width="170px" style="border: 0.5px solid black;">{{$value['user']}}</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$value['unitPrice']}}円</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$value['count']}}件</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$value['price']}}円</td>
                    <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">0件</td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endforeach