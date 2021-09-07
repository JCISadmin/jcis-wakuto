<style>
    /*td用*/
    td {
        border-collapse: collapse;
        border: 0.5px solid black;
    }

    /*td用--空行*/
    td.space{
        height: 40px;
    }

    td.title{
        border: none;
        font-size: 20px;
        height:40px;
        line-height:40px;
        text-align: center;

    }

    /*td用--項目名*/
    td.column{
        height: 30px;
        line-height: 30px;
        text-align: left;
        font-size: 15px;
    }

    /*td用--テキスト*/
    td.text{
        height: 30px;
        line-height: 30px;
        text-align: right;
        font-size: 15px;
    }

    /*table用*/
    .pdf-table {
        border: none;
        margin-left: auto;
        margin-right: auto;
    }

    /*table用--タイトル*/
    .pdf-table-title{
        border-collapse: collapse;
        border: 2px solid black;
        margin-left: auto;
        margin-right: auto;
    }

</style>

<table class="pdf-table">
    <tr>
        <td width="100px" style="font-size:15px;">
            ユーザID
        </td>
        <td width="30px" style="border:none; font-size:15px;">様</td>
    </tr>
</table>

<table class="pdf-table-title">
        <tr>
            <td width="200px" height="35" class="title">
                利用明細
            </td>
        </tr>
</table>
<tr>
    <td class="space">
    </td>
</tr>
<table class="pdf-table">
    <tr>
        <td width="200px" class="column">
            発行日時
        </td>
        <td width="250px" class="text">
            {{$printDate}}
        </td>
    </tr>
    <tr>
        <td width="200px" class="column">
            今月検索件数
        </td>
        <td width="250px" class="text">
            {{number_format($monthSearchCount)}}件
        </td>
    </tr>
    <tr>
        <td width="200px" class="column">
            年間検索件数
        </td>
        <td width="250px" class="text">
            {{number_format($yearSearchCount)}}件
        </td>
    </tr>
    <tr>
        <td width="200px" class="column">
            デポジット残高
        </td>
        <td width="250px" class="text">
            {{number_format($depositBalance)}}円
        </td>
    </tr>
</table>
