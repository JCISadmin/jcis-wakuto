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
                利用状況一覧
            </td>
        </tr>
</table>

<table>
        <tr>
            <td class="sub">
            </td>
        </tr>
</table>

<table class="table_detail">
    <tr>
        <td class="column" width="40px">契約状況</td>
        <td class="column" width="110px">会社名</td>
        <td class="column" width="75px">当社窓口</td>
        <td class="column" width="30px">ID数</td>
        <td class="column" width="60px">契約プラン</td>
        <td class="column" width="80px">検索件数</td>
        <td class="column" width="80px">同一ワード検索件数</td>
        <td class="column" width="65px">金額</td>
    </tr>
</table>

@foreach($userList as $userItem)
    <table class="table_detail">
        <tr>
            <td class="content" width="40px" style="border: 0.5px solid black; text-align: left;">{{$userItem->statusName}}</td>
            <td class="content" width="110px" style="border: 0.5px solid black; text-align: left;">{{$userItem->name}}</td>
            <td class="content" width="75px" style="border: 0.5px solid black; text-align: left;">{{$userItem->chargeName}}</td>
            <td class="content" width="30px" style="border: 0.5px solid black; text-align: right;">
                {{ $userItem->webPlanIds }}
                @if (isset($userItem->webPlanIds) && isset($userItem->apiPlanIds))
                    <br>
                @endif
                {{ $userItem->apiPlanIds }}
            </td>
            <td class="content" width="60px" style="border: 0.5px solid black; text-align: left;">
                {{ $userItem->webPlanName }}
                @if (isset($userItem->webPlanName) && isset($userItem->apiPlanName))
                    <br>
                @endif
                {{ $userItem->apiPlanName }}
            </td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">
                {{ $userItem->webPlanTotalCount }}件
                @if (isset($userItem->webPlanTotalCount) && isset($userItem->apiPlanTotalCount))
                    <br>
                @endif
                {{ $userItem->apiPlanTotalCount }}件
            </td>
            <td class="content" width="80px" style="border: 0.5px solid black; text-align: right;">{{$userItem->dupSearchCount}}件</td>
            <td class="content" width="65px" style="border: 0.5px solid black; text-align: right;">
                {{ $userItem->webTotalPrice }}円
                @if (isset($userItem->webTotalPrice) && isset($userItem->apiTotalPrice))
                <br>
                @endif
                {{ $userItem->apiTotalPrice }}円
            </td>
        </tr>
    </table>
@endforeach