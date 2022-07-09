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
                利用状況詳細
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
        <td class="column" width="180px">ID/担当者名</td>
        <td class="column" width="120px">単価</td>
        <td class="column" width="120px">検索数</td>
        <td class="column" width="120px">金額</td>
    </tr>
</table>

@foreach($detail['report'] as $userItem)
    <table class="table_detail">
        <tr>
            <td class="content" width="180px" style="border: 0.5px solid black; text-align: left;">{{$userItem['user']}}</td>
            <td class="content" width="120px" style="border: 0.5px solid black; text-align: right;">{{$userItem['unitPrice']}}</td>
            <td class="content" width="120px" style="border: 0.5px solid black; text-align: right;">{{$userItem['count']}}件</td>
            <td class="content" width="120px" style="border: 0.5px solid black; text-align: right;">{{$userItem['price']}}円</td>
        </tr>
    </table>
@endforeach