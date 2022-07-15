@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                利用状況一覧画面
            </h1>
        </div>
    </header>
    <main>

        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageUsageStatusSearch') }}">
                @csrf
                <div class="flex">
                    <div class="flex-initial px-4">
                        <label for="searchDate">期間</label>
                        <input type="date" value="{{ $searchDateFrom }}" name="searchDateFrom" id="searchDateFrom"
                               class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <label class="mx-2">-</label>
                        <input type="date" value="{{ $searchDateTo }}" name="searchDateTo" id="searchDateTo"
                               class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="flex-initial px-4">
                        <label for="contractPlan">契約プラン</label>
                        <select name="contractPlan" id="contractPlan"
                                class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="" {{ '' == $contractPlan ? 'selected' : '' }}></option>
                            @foreach($selectList['contractPlan'] as $item)
                                <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId === $contractPlan ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-initial px-4">
                        <label for="chargeName">当社窓口</label>
                        <input type="text" maxlength="20" value="{{ $chargeName }}" name="chargeName" id="chargeName"
                               class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>

                    
                    <div class="flex-initial px-4">
                        <button type="submit"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            表示
                        </button>
                    </div>
                </div>
                
                <div class="flex py-4">
                    <div class="flex-initial px-4">
                        <label for="contractPlan">並び替え</label>
                        <select name="dispType" id="dispType"
                                class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="1" {{ $dispType == 1 ? 'selected' : '' }}>検索件数 昇順</option>
                            <option value="2" {{ $dispType == 2 ? 'selected' : '' }}>検索件数 降順</option>
                        </select>
                    </div>

                </div>
            </form>
        </div>
        
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        @if ($searchDateFrom != '' && $searchDateTo != '')
                        <div class="pb-8 px-5">
                            集計期間 : {{ $searchDateFrom }} - {{ $searchDateTo }}
                        </div>
                        @endif
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg mb-10">
                            <table id="sumTable" class="min-w-full divide-y divide-gray-200">
                                <tbody>
                                    <tr>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">契約中</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-base font-medium border border-r-0 text-center">
                                            <span>{{ $userList->contractCom }}社</span>
                                        </td>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">トライアル中</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-base font-medium border border-r-0 text-center">
                                            <span>{{ $userList->trialCom }}社</span>
                                        </td>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">契約終了</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-base font-medium border border-r-0 text-center">
                                            <span>{{ $userList->contractEndCom }}社</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">検索件数</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-base font-medium border border-r-0 text-center">
                                            <span>{{ $userList->sumSearchCount }}件</span>
                                        </td>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">ID数</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-base font-medium border text-center">
                                            <span>{{ $userList->sumId }}個</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">金額</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-base font-medium border text-center">
                                            <span>{{ $userList->sumPrice }}円</span>
                                        </td>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">金額(税込)</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-base font-medium border text-center">
                                            <span>{{ $userList->sumPriceWithTax }}円</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            No
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            契約状況
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            会社名
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            当社窓口
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            ID数
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            契約プラン
                                        </th>

                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            検索件数
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            同一ワード検索件数
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            金額
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach( $userList as $item)
                                        @php
                                            /* @var  $num */
                                            /* @var  $userList */
                                            /* @var  $loop */
                                            /* @var  $disabled */
                                            $num = $userList->firstItem() + $loop->index;
                                        @endphp

                                        <tr>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border">
                                                {{ $num }}
                                            </td>
                                            <td class="px-3 py-4 w-45 text-sm font-medium border">
                                                {{ $item->statusName }}
                                            </td>
                                            <td class="px-3 py-4 w-45 text-sm font-medium border">
                                                {{ $item->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->chargeName }}
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border">
                                                {{ $item->webPlanIds }}
                                                @if (isset($item->webPlanName) && isset($item->apiPlanName))
                                                    <br>
                                                @endif
                                                {{ $item->apiPlanIds }}
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->webPlanName }}
                                                @if (isset($item->webPlanName) && isset($item->apiPlanName))
                                                <br>
                                                @endif
                                                {{ $item->apiPlanName }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->webPlanTotalCount }}件
                                                @if (isset($item->webPlanTotalCount) && isset($item->apiPlanTotalCount))
                                                <br>
                                                @endif
                                                {{ $item->apiPlanTotalCount }}件
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->dupSearchCount }}件
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->webTotalPrice }}円
                                                @if (isset($item->webTotalPrice) && isset($item->apiTotalPrice))
                                                <br>
                                                @endif
                                                {{ $item->apiTotalPrice }}円
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                                <button type="button" onclick="location.href = '{{ route('manageUsageStatusDetail', ['editId' => $item->companyId]) }}';"
                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    詳細
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                            <div class="w-5/6">
                            {{ $userList->links('paginate') }}
                        </div>
                        <input type="hidden" name="page" value="{{ app('request')->input('page') }}">
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
