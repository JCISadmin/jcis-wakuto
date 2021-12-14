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
        <td class="column" width="200px">年月</td>
        <td class="column" width="200px">種別</td>
        <td class="column" width="140px">件数</td>
    </tr>
</table>

@foreach($detail as $item)
    @php
        /* @var  $detail */
        
    @endphp
    <table class="table_detail">
        <tr>
            <td class="content" width="200px" style="border: none;">{{date_format(new DateTime($item['month']), 'Y/m')}}</td>
            <td class="content" width="200px" style="border: none;"></td>
            <td class="content" width="140px" style="border: 0.5px solid black; text-align: right;">{{$item['totalCount']}}</td>
        </tr>   

        @foreach($item['userInfo'] as $value)
            @php
                /* @var  $item */
            @endphp
            <tr>
                <td class="content" width="200px" style="border: none"></td>
                <td class="content" width="200px" style="border: 0.5px solid black;">{{$value['user']}}</td>
                <td class="content" width="140px" style="border: 0.5px solid black; text-align: right;">{{$value['count']}}</td>
            </tr>
        @endforeach
    </table>
@endforeach