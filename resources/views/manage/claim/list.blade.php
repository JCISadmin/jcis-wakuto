@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                請求一覧画面
            </h1>
        </div>
    </header>

    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageClaimSearch') }}">
                @csrf
                <div class="flex">
                    <div class="flex-initial px-4">
                        <label for="claimMonth">請求月</label>
                        <input type="month" value="{{ $claimMonth }}" name="claimMonth" id="claimMonth"
                               class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="flex-initial px-4">
                        <label for="companyName">会社名</label>
                        <input type="text" maxlength="20" value="{{ $companyName }}" name="companyName" id="companyName"
                               class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="flex-initial px-4">
                        <button type="submit"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            検索
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @if($claimMonth !== '')
            <form id="listForm" method="post" action="{{ route('manageClaimBulkMail') }}">
                @csrf
                <input type="hidden" name="from" value="list">

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-0">
                    <div class="text-right">
                        <button type="button" id="btnExport"
                                onclick="btnAction('export', '')"
                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            エクスポート
                        </button>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-green-500">
                                            <tr>
                                                <th scope="col" class="px-1 py-3 text-left text-xs font-medium text-white border">
                                                </th>
                                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                    No
                                                </th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                    会社名
                                                </th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                    請求番号
                                                </th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                    発行日
                                                </th>
                                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                    支払期限
                                                </th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                    送付期限
                                                </th>
                                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                    支払金額(税込）
                                                </th>
                                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border w-32">
                                                    メモ
                                                </th>
                                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach( $claimList as $item)
                                                @php
                                                    /* @var  $num */
                                                    /* @var  $claimList */
                                                    /* @var  $loop */
                                                    /* @var  $disabled */
                                                    $num = $claimList->firstItem() + $loop->index;
                                                    $disabled = 'pointer-events: none';
                                                @endphp

                                                <tr>
                                                    <td class="px-1 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                                        <label>
                                                            <input type="checkbox" name="exportFlg[{{$num}}]" id="exportFlg_{{$num}}" value="{{ $item->companyId }}">
                                                        </label>
                                                    </td>
                                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ $num }}
                                                    </td>
                                                    <td class="px-1 py-2 whitespace-nowrap text-sm text-center font-medium border">
                                                        <div class="flex">
                                                            <div class="py-1 px-1">
                                                                @if ($item->claimStatus === 1)
                                                                    <button type="button" id="btnClaim" 
                                                                            onclick="btnAction('claim', '{{$item->companyId}}', '{{$item->claimStatus}}')"
                                                                            class="px-3 py-2 disabled:opacity-50 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                            請求済
                                                                    </button>
                                                                @else
                                                                    <button type="button" id="btnClaim" 
                                                                            onclick="btnAction('claim', '{{$item->companyId}}', '{{$item->claimStatus}}')"
                                                                            class="px-3 py-2 opacity-50 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                            未請求
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            <div class="py-1 px-1">
                                                                @if ($item->paymentStatus === 1)
                                                                    <button type="button" id="btnPayment"
                                                                            onclick="btnAction('payment', '{{$item->companyId}}', '{{$item->paymentStatus}}')"
                                                                            class="px-3 py-2 disabled:opacity-50 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                            入金済
                                                                    </button>
                                                                @elseif ($item->claimStatus === 1)
                                                                    <button type="button" id="btnPayment"
                                                                            onclick="btnAction('payment', '{{$item->companyId}}', '{{$item->paymentStatus}}')"
                                                                            class="px-3 py-2 opacity-50 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                            未入金
                                                                    </button>
                                                                @else
                                                                    <button type="button" id="btnPayment" disabled
                                                                            onclick="btnAction('payment', '{{$item->companyId}}', '{{$item->paymentStatus}}')"
                                                                            class="px-3 py-2 disabled:opacity-50 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                            未入金
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ $item->mUserName }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ $item->claimNo }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ '' == $item->claimDate ? '' : date_format(new Datetime($item->claimDate), 'Y/m/d') }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ '' == $item->paymentDate ? '' : date_format(new Datetime($item->paymentDate), 'Y/m/d') }}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                        {{ '' == $item->claimDeliveryDate ? '' : $item->claimDeliveryDate }}
                                                    </td>
                                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border ">
                                                        {{ $item->priceWithTax }}
                                                    </td>
                                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-left font-medium border overflow-hidden max-w-0">
                                                    <div title="{!! str_replace( "\r\n", "&#13;&#10;" ,$item->claimMemo ) !!}">
                                                        {{ $item->claimMemo }}
                                                    </div>
                                                    </td>

                                                    <td class="px-1 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                                    <div class="flex">
                                                        <div class="py-1 px-1">
                                                            <button type="button" onclick="location.href = '{{ route('manageClaimEdit',['editId'=>$item->companyId]) }}';"
                                                                    class="px-5 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                詳細
                                                            </button>
                                                        </div>
                                                        <div class="py-1 px-1">
                                                            <button type="submit" id="btnPdf" formtarget="_blank" onclick="btnAction('pdf', '{{$item->companyId}}')"
                                                                    class="px-3 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                プレビュー
                                                            </button>
                                                        </div>
                                                                </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div class="w-5/6">
                    {{ $claimList->links('paginate') }}
                </div>
                <input type="hidden" name="page" value="{{ app('request')->input('page') }}">
                
                <div class="w-1/6 text-right">
                <button type="submit" id="btnMail" onclick="btnAction('bulkMail', '')"
                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            一括メール送信
                        </button>
                    </div>
                </div>
            </form>
        @endif


    </main>

    <script>

        function btnAction(type, editId, editStatus) {
            let action = '{{ route('manageClaimExport') }}';
            let targetForm = $('#listForm');

            if (type === 'claim') {
                if(editStatus == 1){
                    action = '{{ route('manageClaimNotClaim') }}' + '/' + editId;
                    targetForm.attr('action', action);
                } else {
                    action = '{{ route('manageClaimClaim') }}' + '/' + editId;
                    targetForm.attr('action', action);
                }

            } else if (type === 'payment') {
                if(editStatus == 1){
                    action = '{{ route('manageClaimNotPayment') }}' + '/' + editId;
                    targetForm.attr('action', action);
                } else {
                    action = '{{ route('manageClaimPayment') }}' + '/' + editId;
                    targetForm.attr('action', action);
                }
            } else if (type === 'bulkMail') {
                action = '{{ route('manageClaimBulkMail') }}';
                targetForm.attr('action', action);

            } else if (type === 'pdf') {
                action = '{{ route('manageClaimPdf') }}' + '/' + editId;
                targetForm.attr('action', action);


            } else {
                targetForm.attr('action', action);
            }

            targetForm.submit();
        }

    </script>

@endsection