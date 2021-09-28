@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                請求詳細画面
            </h1>
        </div>
    </header>
    <main>
        @include('msg')

        <form id="listForm" method="post" action="{{ route('manageClaimUpdate') }}">
            @csrf

            <input type="hidden" name="from" value="edit">

            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-1 py-3 text-left text-xs font-medium text-white border border-r-0">
                                                
                                            </th>
                                            <th scope="col" class="px-1 py-3 text-left text-xs font-medium text-white border border-l-0">
                                                
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求番号
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求日
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                支払期日
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求金額(税込)
                                            </th>
                                        </tr>
                                    </thead>

                                    @php
                                    /* @var  $disabled */
                                    $disabled = 'pointer-events: none';
                                    @endphp
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium border border-r-0">
                                                <div class="py-1">
                                                    <button type="button" id="btnClaim" {{ $claimList[0]->claimStatus === 1 ? 'disabled' : '' }}
                                                            onclick="btnAction('claim', '{{$claimList[0]->companyId}}')"
                                                            class="px-6 py-2 w-28 disabled:opacity-50 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                        {{ $claimList[0]->claimStatus === 1 ? '請求済' : '請求未済' }}
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium border border-l-0">

                                                <div class="py-1">
                                                    @php
                                                        /* @var $claimList */
                                                        if ($claimList[0]->paymentStatus === 1) {
                                                            $dispPayment = '入金済';
                                                            $btnMode = 'disabled';

                                                        } elseif  ($claimList[0]->claimStatus === 1) {
                                                            $dispPayment = '入金未済';
                                                            $btnMode = '';

                                                        } else {
                                                            $dispPayment = '入金未済';
                                                            $btnMode = 'disabled';

                                                        }

                                                    @endphp

                                                    <button type="button" id="btnPayment" {{ $btnMode }}
                                                            onclick="btnAction('payment', '{{$claimList[0]->companyId}}')"
                                                            class="px-6 py-2 w-28 disabled:opacity-50 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                        {{ $dispPayment }}
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->claimNo }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->claimDate === null ? '' : date_format(new Datetime($claimList[0]->claimDate), 'Y/m/d') }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="date" name="paymentDate" id="paymentDate"
                                                        value="{{ old('paymentDate', $claimList[0]->paymentDate) }}"
                                                        class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                {{ $claimList[0]->price + round($claimList[0]->price * $claimList[0]->tax / 100) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                請求補正理由
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                請求補正金額
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="20" value="{{ old('adjustNote', $claimList[0]->adjustNote) }}" name="adjustNote" id="adjustNote"
                                                        class="px-2 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="10" value="{{ old('adjustPrice', $claimList[0]->adjustPrice) }}" name="adjustPrice" id="adjustPrice"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                会社名
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                郵便番号
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                会社住所
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                代表電話番号
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                当社窓口
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                当社窓口Email
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->name }}
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->postCode === null ? '' : substr_replace($claimList[0]->postCode, '-', 3, 0) }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->address }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->tel }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->chargeName }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->chargeMail }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求者名
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求者部署・役職
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求者電話番号
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求先TO
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                請求先CC
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->claimName }}
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->claimDepartmentJob }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->claimTel }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->claimMailTo }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $claimList[0]->claimMailCc }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                /* @var  $passFlg*/
                $passFlg = false;
            @endphp
            @foreach( $planList as $item)
                @php
                    /* @var  $num */
                    /* @var  $planList */
                    /* @var  $loop */

                    $num = $loop->iteration;
                    if($passFlg){
                        $num = $num - 1;
                    }
                @endphp

                @if( is_null($item['companyId']) === true )
                    @php
                        $passFlg = true;
                    @endphp
                @else
                    @php
                        $passFlg = false;
                    @endphp

                    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                        <table id="webTable" class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-green-500">
                                                <tr>
                                                    <th scope="col" class="px-1 py-3 text-left text-xs font-medium text-white border">
                                                        No
                                                    </th>
                                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                        契約プラン
                                                    </th>
                                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                        契約形態
                                                    </th>
                                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                        ID個数
                                                    </th>
                                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                        ID代
                                                    </th>
                                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                        検索単価
                                                    </th>
                                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                        年件数
                                                    </th>
                                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                        月間検索数
                                                    </th>
                                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                        デポジット残高
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-1 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                        {{ $num }}
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ $item['contractPlanName'] }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ $item['contractTypeName'] }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                        {{ $item['ids'] }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                        {{ $item['idUnitPrice'] }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                        {{ $item['searchUnitPrice'] }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                        {{ $item['searchCount'] }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                        {{ $item['monthSearchCount'] }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                    <input type="text" maxlength="10" value="{{ old('deposit.'. $item['planType'], $item['deposit']) }}" name="deposit[{{$item['planType']}}]" id="deposit_{{$item['planType']}}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        @if( $item['userDetail'] !== [] )
                                            <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8 ml-0">
                                                <div class="flex flex-col">
                                                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                                <table id="webDetail" class="min-w-full divide-y divide-gray-200">
                                                                    <thead class="bg-green-500">
                                                                        <tr>
                                                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                                                No
                                                                            </th>
                                                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                                                ユーザーID
                                                                            </th>
                                                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                                                月間検索数
                                                                            </th>
                                                                        </tr>
                                                                    </thead>

                                                                    @foreach( $item['userDetail'] as $userItem)
                                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                                            <tr>
                                                                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                                    {{ $userItem['no'] }}
                                                                                </td>
                                                                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                                                    {{ $userItem['userId'] }}
                                                                                </td>
                                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                                    {{ $userItem['monthSearchCount'] }}
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    @endforeach
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="py-2 text-center">
                        <button type="button" id="btnPdf" onclick="btnAction('pdf', '{{$claimList[0]->companyId}}')"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            請求書プレビュー
                        </button>

                        <button type="button" id="btnUpdate" onclick="btnAction('update', '{{$claimList[0]->companyId}}')"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            更新
                        </button>
                    </div>
                    <div class="py-2 text-center">
                        <button type="button" id="btnMail" onclick="btnAction('mail', '{{$claimList[0]->companyId}}')"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            メール送信
                        </button>

                        <button type="button" id="btnBack" onclick="location.href = '{{ route('manageClaimList')}}';"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            一覧に戻る
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

<script>

    function btnAction(type, editId) {
        let action = '{{ route('manageClaimUpdate') }}' + '/' + editId;
        let targetForm = $('#listForm');

        if (type === 'claim') {
            action = '{{ route('manageClaimClaim') }}' + '/' + editId;
            targetForm.attr('action', action);

        } else if (type === 'payment') {
            action = '{{ route('manageClaimPayment') }}' + '/' + editId;
            targetForm.attr('action', action);

        } else if (type === 'pdf') {
            action = '{{ route('manageClaimPdf') }}' + '/' + editId;
            targetForm.attr('action', action);

        } else if (type === 'mail') {
            action = '{{ route('manageClaimMail') }}' + '/' + editId;
            targetForm.attr('action', action);

        } else {        
            targetForm.attr('action', action);
        }

        targetForm.submit();

    }

</script>

    
@endsection