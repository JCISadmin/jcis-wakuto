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

    /*td用--項目名*/
    td.column{
        border: 0.5px solid black;
        text-align: left;
        height: 30px;
        line-height: 30px;
        font-size: 15px;
        background-color: #f5f5f5;
    }

    /*td用--テキスト*/
    td.text{
        border: 0.5px solid black;
        text-align: right;
        height: 30px;
        line-height: 30px;
        font-size: 15px;
    }
</style>

<table style="border: none;">
    <tr>
        <td width="250px" style="font-size: 15px; height: 20px; line-height: 20px; border: 0.5px solid #a9a9a9;">
            {{$userId}}
        </td>
        <td width="30px" style="font-size: 15px; height: 20px; line-height: 20px; border: none;">様</td>
    </tr>
</table>

<tr>
    <td  style="height: 20px;">
    </td>
</tr>

<table style="border: 2px solid black;" >
        <tr>
            <td class="title">
                利用明細
            </td>
        </tr>
</table>

<tr>
    <td style="height: 40px;">
    </td>
</tr>

<table style="border: none;">
    <tr>
        <td width="270px" class="column">
            発行日時
        </td>
        <td width="270px" class="text">
            {{$printDate}}
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
    <tr>
        <td width="270px" class="column">
            デポジット残高
        </td>
        <td width="270px" class="text">
            {{number_format($depositBalance)}}円
        </td>
    </tr>
</table>