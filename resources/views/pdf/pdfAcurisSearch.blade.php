<style>
    /*td用-タイトル*/
    td {
      height: 15px;
    }

    td.title{
        border: none;
        text-align: center;
        width: 540px;
        height: 40px;
        line-height: 40px;
        font-size: 20px;
    }

    /*td用--テキスト*/
    td.text{
        border: 0.5px solid black;
        text-align: right;
        height: 30px;
        line-height: 30px;
        font-size: 15px;
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
        border-right: 0.5px solid black;
        border-left: 0.5px solid black;
        border-top: 0.5px solid black;
        text-align: left;
        height: 20px;
        line-height: 20px;
        font-size: 8px;
    }

    td.content2{
        border-right: 0.5px solid black;
        border-left: 0.5px solid black;
        border-top: 0.5px solid black;
        text-align: center;
        height: 20px;
        line-height: 20px;
        font-size: 8px;
    }

    table.table_detail{
        border: 0.5px solid black;
    }

</style>

<div></div>
<div></div>

<table>
    @if (isset($keyword['company']))
    <tr>
        <td width="100px"></td>
        <td width="360px">法人(Corporation)</td>
    </tr>
        @foreach ($keyword['company'] as $isExist => $items)
        @foreach ($items as $item)
        <tr>
            <td width="100px"></td>
            @if($isExist === 'error')
                <td width="360px">検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　エラーが発生しました。検索代は発生しません。</td>
            @else
                <td width="360px">検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし"; }}</td>
            @endif
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
        <td width="360px">個人(Person)</td>
    </tr>
        @foreach ($keyword['person'] as $isExist => $items)
        @foreach ($items as $item)
        <tr>
            <td width="100px"></td>
            @if($isExist === 'error')
                <td width="360px">検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　エラーが発生しました。検索代は発生しません。</td>
            @else
                <td width="360px">検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし"; }}</td>
            @endif
        </tr>
        @endforeach
        @endforeach
    @endif
</table>

<tr>
    <td  style="height: 20px;"></td>
</tr>

<table class="table_detail">
    <tr>
        <td class="column" width="135px">Name(氏名)</td>
        <td class="column" width="90px">Date of Birth(生年月日)</td>
        <td class="column" width="135px">DataSets(データセット)</td>
        <td class="column" width="45px">Gender(性別)</td>
        <td class="column" width="90px">Nationality(国籍)</td>
        <td class="column" width="45px">Score(スコア)</td>
    </tr>

    @foreach ($result as $item)
        <tr>
            <td class="content" width="135px">{{ $item['name'] }}</td>
            <td class="content" width="90px">{{ isset($item['datesOfBirth']) ?  $item['datesOfBirth'] : ''}}</td>
            <td class="content" width="135px">{{ $item['datasets'] }}</td>
            <td class="content" width="45px">{{ isset($item['gender']) ?  $item['gender'] : ''}}</td>
            <td class="content" width="90px">{{ $item['countries'] }}</td>
            <td class="content2" width="45px">{{ $item['score'] }}</td>
        </tr>
    @endforeach
</table>



















<tr>
    <td  style="height: 10px;"></td>
</tr>

<p style="text-align:left;">
</p>
