<!--suppress CssNonIntegerLengthInPixels -->
<style>
    td{
        border-collapse: collapse;
    }

    /*td用-タイトル*/
    td.header{
        border: none;
        height: 10px;
        line-height: 10px;
        font-size: 8px;
    }


    /*td用-明細項目*/
    td.detail_header{
        text-align: center;
        border: 0.1px solid #339999;
        background-color: #339999;
        height: 15px;
        line-height: 15px;
        font-size: 8px;
    }

    /*td用--明細内容*/
    td.detail_content{
        text-align: right;
        border: 0.1px solid #339999;
        height: 15px;
        line-height: 15px;
        font-size: 8px;
    }

    /*td用--明細合計*/
    td.detail_total{
        text-align: right;
        border: 0.1px solid #339999;
        height: 15px;
        line-height: 15px;
        font-size: 8px;
    }

    td.footer{
        border: 0.1px solid #339999;
        height: 15px;
        line-height: 15px;
        font-size: 8px;
    }

    tr.oddRow{
        background-color: #AFEEEE;
    }

    table{
        border: none;
    }
</style>


<table>
    <tr>
        <td colspan="6" style="width: 460px;" class="header"></td>
        <td colspan="1" style="width: 80px;" class="header">{{date_format(new DateTime(), 'Y年m月d日')}}</td>
    </tr>
    <tr>
        <td colspan="5" style="width: 380px; height: 40px; font-size: 20px; color: #339999">請 求 書</td>
        <td colspan="1" style="width: 80px;  height: 40px; font-size: 8px;">請求番号：</td>
        <td colspan="1" style="width: 80px;  height: 40px; font-size: 8px;">{{$claimInfo['claimNo']}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 220px; border-bottom: solid medium #339999; font-size: 11px;" class="header">{{$claimInfo['name']}}</td>
        <td colspan="1" style="width: 50px; border-bottom: solid medium #339999;" class="header">御中</td>
        <td colspan="4" rowspan ="3" style="width: 270px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="3" style="width: 270px;" class="header">
            @if (!is_null($claimInfo['claimName']))
                {{$claimInfo['claimDepartmentJob'].' '}}
            @endif    
            {{ $claimInfo['claimName'] }}
            @if (!is_null($claimInfo['claimName']))
                様
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="7" style="width: 540px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="3" style="width: 270px;" class="header">

            @foreach($detail as $key => $value)
                @php
                    /* @var  $detail */
                    /* @ver  $loop */
                @endphp

                @if($loop !== 1)
                    <br>
                @endif

                @if(array_key_exists('adjust', $value) === false)
                    件名：{{$key}}
                @endif
            @endforeach

        </td>
    </tr>
    <tr>
        <td colspan="7" style="width: 540px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="7" style="width: 540px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="3" style="width: 270px;" class="header">下記のとおりご請求申し上げます</td>
        <td colspan="3" style="width: 190px;" class="header"></td>
        <td colspan="1" rowspan ="3" style="width: 80px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="1" style="width: 80px; background-color:#339999; color: white; text-align: center;" class="header">
            ご請求金額
        </td>
        <td colspan="2" style="width: 190px; font-size: 10px; border-bottom: solid medium #339999; text-align: center;" class="header">
            ¥{{number_format($claimInfo['priceWithTax'])}}-
        </td>
        <td colspan="3" style="width: 190px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="1" style="width: 80px;" class="header">お支払い期限：</td>
        <td colspan="2" style="width: 190px;" class="header">{{ is_null($claimInfo['paymentDate']) ? '': date_format(new DateTime($claimInfo['paymentDate']), 'Y年m月d日') }}
        </td>
        <td colspan="3" style="width: 190px; text-indent:-1.5em;" class="header">
        </td>
    </tr>
    <tr>
        <td colspan="7" style="width: 540px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="7" style="width: 540px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="7" style="width: 540px;" class="header"></td>
    </tr>
    <tr>
        <td colspan="7" style="width: 540px;" class="header"></td>
    </tr>
</table>

<div style="height: 40px;"></div>


<table>
    <tr>
        <td class="detail_header" style="width: 270px; ">品番</td>
        <td class="detail_header" style="width: 80px; background-color: #339999; border-left: solid 5px white;" >数量</td>
        <td class="detail_header" style="width: 80px; background-color: #339999; border-left: solid 5px white;">単価</td>
        <td class="detail_header" style="width: 110px; background-color: #339999; border-left: solid 5px white;">金額</td>
    </tr>
    @php

        $row = 0;

    @endphp
    @foreach($detail as $key => $detailItem)
        @php

            /** @var $i */
            $i = 1;

            /** @var $row */
            if($row % 2 === 1){
                $addClass = 'oddRow';
            }else{
                $addClass = '';
            }

        @endphp
        @if(array_key_exists('adjust',$detailItem) === false)
            <tr class="{{$addClass}}">
                <td class="detail_content" style="width: 270px; text-align: left;">{{$key}}</td>
                <td class="detail_content" style="width: 80px;"></td>
                <td class="detail_content" style="width: 80px;"></td>
                <td class="detail_content" style="width: 110px;"></td>
            </tr>
            @php
                /** @var $row */
                $row ++;
            @endphp
        @endif

        @foreach($detailItem as $key => $value)

            @php
                /** @var $row */
                /** @var $key */
                /** @var $i */

                if($row % 2 === 1){
                    $addClass = 'oddRow';
                }else{
                    $addClass = '';
                }

                $prefix = '';
                if($key !== 'adjust'){
                    $prefix = $i . '. ';
                }
            @endphp

            <tr class="{{$addClass}}">
                <td class="detail_content" style="width: 270px; text-align: left;">{{ $prefix . $value['itemName'] }}</td>
                <td class="detail_content" style="width: 80px;">{{$value['amount']}}</td>
                <td class="detail_content" style="width: 80px;">{{is_null($value['unitPrice']) ? '' : number_format($value['unitPrice'])}}</td>
                <td class="detail_content" style="width: 110px;">{{number_format($value['price'])}}</td>
            </tr>
            @php
                /** @var $i */
                /** @var $row */
                $i ++;
                $row ++;
            @endphp
        @endforeach
    @endforeach

    <tr>
        <td class="detail_total" colspan="1" style="width: 270px; border: none;"></td>
        <td class="detail_total" colspan="2" style="width: 160px;">小計</td>
        <td class="detail_total" colspan="1" style="width: 110px;">{{number_format($claimInfo['priceWithoutTax'])}}</td>
    </tr>
    <tr>
        <td class="detail_total" colspan="1" style="width: 270px; border: none;"></td>
        <td class="detail_total" colspan="2" style="width: 160px;">消費税({{$claimInfo['tax']}}%)</td>
        <td class="detail_total" colspan="1" style="width: 110px;">{{number_format($claimInfo['taxPrice'])}}</td>
    </tr>
    <tr>
        <td class="detail_total" colspan="1" style="width: 270px; border: none;"></td>
        <td class="detail_total" colspan="2" style="width: 160px;">合計</td>
        <td class="detail_total" colspan="1" style="width: 110px;">{{number_format($claimInfo['priceWithTax'])}}</td>
    </tr>
</table>

<div style="height: 40px;"></div>

<table>
    <tr>
        <td class="footer" style="width: 80px; background-color:#339999; color: white; text-align: center;">備考欄</td>
        <td class="footer" style="width: 460px; border:none;"></td>
    </tr>
    <tr>
        <td class="footer" style="width: 540px;">
            ※恐れ入りますが、振込手数料は貴社ご負担にてお願い致します。
        </td>
    </tr>
</table>
<div style="height: 1px;"></div>
<table>
    <tr>
        <td class="footer" style="width: 80px; background-color:#339999; color: white; text-align: center;">お振込先</td>
        <td style="width: 460px; border:none;"></td>
    </tr>
    <tr>
        <td class="footer" style="width: 540px;">
            {{$companyInfo['bank']}}
        </td>
    </tr>
</table>
