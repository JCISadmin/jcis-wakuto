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
        <td class="column" width="200px">ID/担当者名</td>
        <td class="column" width="90px">検索数</td>
        <td class="column" width="90px">同一ワード検索数</td>
        <td class="column" width="110px">金額</td>
    </tr>
</table>

@foreach($detail as $yearItem)
    
    <table class="table_detail">
        <tr>
            <td class="content" width="50px">{{ $yearItem['year'] }}</td>
            <td class="content" width="200px"></td>
            <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">{{$yearItem['yearTotalCount']}}件</td>
            <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">0件</td>
            <td class="content" width="110px" style="border: 0.5px solid black; text-align: right;">{{$yearItem['yearTotalCount'] * 100}}円</td>
        </tr>

        @foreach($yearItem['userInfo'] as $yearValue)
        <tr>
            <td class="content" width="50px" style="border: none"></td>
            <td class="content" width="200px" style="border: 0.5px solid black;">{{$yearValue['user']}}</td>
            <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">{{$yearValue['count']}}件</td>
            <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">0件</td>
            <td class="content" width="110px" style="border: 0.5px solid black; text-align: right;">{{$yearValue['count'] * 100}}円</td>
        </tr>
        @endforeach
    </table>

    @foreach ($yearItem['monthList'] as $item)
        <table class="table_detail">
            <tr>
                <td class="content" width="50px" style="border: none;">{{date_format(new DateTime($item['month']), 'Y/m')}}</td>
                <td class="content" width="200px" style="border: none;"></td>
                <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">{{$item['monthTotalCount']}}件</td>
                <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">0件</td>
                <td class="content" width="110px" style="border: 0.5px solid black; text-align: right;">{{$item['monthTotalCount'] * 100}}円</td>
            </tr>   

            @foreach($item['userInfo'] as $value)
                @php
                    /* @var  $item */
                @endphp
                <tr>
                    <td class="content" width="50px" style="border: none"></td>
                    <td class="content" width="200px" style="border: 0.5px solid black;">{{$value['user']}}</td>
                    <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">{{$value['count']}}件</td>
                    <td class="content" width="90px" style="border: 0.5px solid black; text-align: right;">0件</td>
                    <td class="content" width="110px" style="border: 0.5px solid black; text-align: right;">{{$value['count'] * 100}}円</td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endforeach