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
                                                {{ is_null($claimList[0]->claimDate) ? '' : date_format(new Datetime($claimList[0]->claimDate), 'Y/m/d') }}
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
                                                {{ is_null($claimList[0]->postCode) ? '' : substr_replace($claimList[0]->postCode, '-', 3, 0) }}
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


            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="claimDetailTable" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                品番
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                数量
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                単価
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                金額
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($expenseList as $expenseItem)
                                        @php
                                            /* @var  $num */
                                            /* @var  $expenseList */
                                            /* @var  $loop */
                                            $num =   $loop->index + 1;
                                        @endphp

                                        <tr>
                                            <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="hidden" value="0" name="detail[expense][ {{ $num }} ][useFlg]" id="expense_useFlg_{{ $num }}">
                                                <input type="checkbox" value="1" name="detail[expense][ {{ $num }} ][useFlg]" id="expense_useFlg_{{ $num }}" {{ $expenseItem['useFlg'] === 1 ? 'checked="checked"' : '' }}
                                                        class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="w-1/2 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" value="{{ old(sprintf('detail.expense.%d.itemName', $num), $expenseItem['itemName']) }}" name="detail[expense][ {{ $num }} ][itemName]" id="expense_itemName_{{ $num }}"
                                                        class="px-2 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>

                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                            @if($expenseItem['type'] !== 'title')
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.expense.%d.amount', $num), $expenseItem['amount']) }}" name="detail[expense][ {{ $num }} ][amount]" id="expense_amount_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            @else
                                                <input type="hidden" value="{{ old(sprintf('detail.expense.%d.amount', $num), $expenseItem['amount']) }}" name="detail[expense][ {{ $num }} ][amount]" id="expense_amount_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            @endif
                                            </td>

                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                            @if($expenseItem['type'] !== 'title')
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.expense.%d.unitPrice', $num), $expenseItem['unitPrice']) }}" name="detail[expense][ {{ $num }} ][unitPrice]" id="expense_unitPrice_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            @else
                                                <input type="hidden" value="{{ old(sprintf('detail.expense.%d.unitPrice', $num), $expenseItem['unitPrice']) }}" name="detail[expense][ {{ $num }} ][unitPrice]" id="expense_unitPrice_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            @endif
                                            </td>

                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                            @if($expenseItem['type'] !== 'title')
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.expense.%d.price', $num), $expenseItem['price']) }}" name="detail[expense][ {{ $num }} ][price]" id="expense_price_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            @else
                                                <input type="hidden" value="{{ old(sprintf('detail.expense.%d.price', $num), $expenseItem['price']) }}" name="detail[expense][ {{ $num }} ][price]" id="expense_price_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            @endif
                                            </td>
                                            <input type="hidden" value="{{ old(sprintf('detail.expense.%d.type', $num), $expenseItem['type']) }}" name="detail[expense][ {{ $num }} ][type]" id="expense_type_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                請求補正理由
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                請求補正数量
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                請求補正単価
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                請求補正金額
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">

                                        @foreach ($expenseAdjustList as $expenseAdjustItem)
                                        @php
                                            /* @var  $num */
                                            /* @var  $expenseAdjustList */
                                            /* @var  $loop */
                                            $num =   $loop->index + 1;
                                        @endphp

                                        <tr>
                                            <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="hidden" value="0" name="detail[adjust][ {{ $num }} ][useFlg]" id="adjust_useFlg_{{ $num }}">
                                                <input type="checkbox" value="1" name="detail[adjust][ {{ $num }} ][useFlg]" id="adjust_useFlg_{{ $num }}" {{ $expenseAdjustItem['useFlg'] === 1 ? 'checked="checked"' : '' }}
                                                        class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="w-1/2 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" value="{{ old(sprintf('detail.adjust.%d.itemName', $num), $expenseAdjustItem['itemName']) }}" name="detail[adjust][ {{ $num }} ][itemName]" id="adjust_itemName_{{ $num }}"
                                                        class="px-2 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.adjust.%d.amount', $num), $expenseAdjustItem['amount']) }}" name="detail[adjust][ {{ $num }} ][amount]" id="adjust_amount_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.adjust.%d.unitPrice', $num), $expenseAdjustItem['unitPrice']) }}" name="detail[adjust][ {{ $num }} ][unitPrice]" id="adjust_unitPrice_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.adjust.%d.price', $num), $expenseAdjustItem['price']) }}" name="detail[adjust][ {{ $num }} ][price]" id="adjust_price_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <input type="hidden" value="{{ old(sprintf('detail.adjust.%d.type', $num), $expenseAdjustItem['type']) }}" name="detail[adjust][ {{ $num }} ][type]" id="adjust_type_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                        </tr>
                                        @endforeach


                                        @for ($num = 1; $num <= 5 - count($expenseAdjustList) ; $num++)
                                        
                                        <tr>
                                            <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="hidden" value="0" name="detail[adjust][ {{ $num }} ][useFlg]" id="adjust_useFlg_{{ $num }}">
                                                <input type="checkbox" value="1" name="detail[adjust][ {{ $num }} ][useFlg]" id="adjust_useFlg_{{ $num }}"
                                                        class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="w-1/2 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" value="{{ old(sprintf('detail.adjust.%d.itemName', $num)) }}" name="detail[adjust][ {{ $num }} ][itemName]" id="adjust_itemName_{{ $num }}"
                                                        class="px-2 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.adjust.%d.amount', $num), 0) }}" name="detail[adjust][ {{ $num }} ][amount]" id="adjust_amount_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.adjust.%d.unitPrice', $num), 0) }}" name="detail[adjust][ {{ $num }} ][unitPrice]" id="adjust_unitPrice_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <input type="text" maxlength="10" value="{{ old(sprintf('detail.adjust.%d.price', $num), 0) }}" name="detail[adjust][ {{ $num }} ][price]" id="adjust_price_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <input type="hidden" value="adjust" name="detail[adjust][ {{ $num }} ][type]" id="adjust_type_{{ $num }}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                        </tr>

                                        @endfor

                                    </tbody>
                                </table>


                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                備考欄
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                <textarea name="claimNote" id="claimNote"
                                                        class="px-2 py-2 w-full text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                                        rows="3" wrap="soft">{{ old('claimNote', $claimList[0]->claimNote) }}</textarea>
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
                                                    @if( $item['contractTypeId'] === App\Models\BaseModel::DEPOSIT_USE_PLAN_TYPE)
                                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                            デポジット不足
                                                        </th>
                                                    @endif
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
                                                    <input type="text" maxlength="10" value="{{ old( $item['planType'].'.deposit', $item['deposit']) }}" name="{{$item['planType']}}[deposit]" id="deposit_{{$item['planType']}}"
                                                        class="px-2 py-2 w-full text-right border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    </td>
                                                    @if( $item['contractTypeId'] === App\Models\BaseModel::DEPOSIT_USE_PLAN_TYPE)
                                                        <td colspan="2" class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                            {{ $item['overageCharges'] }}
                                                        </td>
                                                    @endif
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