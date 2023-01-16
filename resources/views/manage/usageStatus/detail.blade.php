@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                利用状況詳細画面
            </h1>
        </div>
    </header>
    <main>
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            会社名:{{ $companyName }}
        </div>

        @include('msg')

        @if(is_null($userDetailList['contractPlan']['web']) === false)
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                システム契約
            </h1>
        </div>
        <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="webTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            契約プラン
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            契約形態
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            トライアル開始日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用開始日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用更新日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用終了通知日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用終了予定日
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['contractPlanName'] }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['contractDetail']['contractTypeName'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['startTrial']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['startTrial']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useStartDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useStartDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useUpdateDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useUpdateDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useEndAlertDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndAlertDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useEndDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndDate']), 'Y/m/d') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="webTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            ID個数
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            ID代
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            検索単価
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            年検索数
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            デポジット残高
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            トライアル検索単価
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['ids'] }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['contractDetail']['idUnitPrice'] }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['contractDetail']['searchUnitPrice'] }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['contractDetail']['searchCount'] }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['deposit'] }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['trialSearchUnitPrice'] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(is_null($userDetailList['contractPlan']['api']) === false)
        <div class="pt-2"></div>
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                API検索契約
            </h1>
        </div>
        <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="webTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            契約プラン
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            契約形態
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            トライアル開始日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用開始日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用更新日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用終了通知日
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            利用終了予定日
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['contractPlanName'] }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['contractDetail']['contractTypeName'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['startTrial']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['startTrial']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useStartDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useStartDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useUpdateDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useUpdateDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useEndAlertDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndAlertDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useEndDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndDate']), 'Y/m/d') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="webTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            ID個数
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            ID代
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            検索単価
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            年検索数
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            デポジット残高
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            トライアル検索単価
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['ids'] }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['contractDetail']['idUnitPrice'] }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['contractDetail']['searchUnitPrice'] }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['contractDetail']['searchCount'] }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['deposit'] }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['trialSearchUnitPrice'] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if ($detail['report'] !== [])
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            ID/担当者名
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            単価
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            検索件数
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            金額
                                        </th>
                                        <th scope="col" class="w-40 px-5 py-3 text-left text-xs font-medium text-white border">
                                            同一ワード検索件数
                                        </th>
                                    </tr>
                                </thead>
                                @foreach ($detail['report'] as $userItem)
                                    <tbody class="bg-white">
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$userItem['userId']}} / {{$userItem['userName']}}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$userItem['unitPrice']}}円
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$userItem['count']}}件
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$userItem['price']}}円
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                            {{$userItem['dupCount']}}件
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                                <tbody class="bg-white">
                                    <tr>
                                        <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        </td>
                                        <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                            {{$detail['totalSearchCount']}}件
                                        </td>
                                        <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                            {{$detail['totalPrice']}}円
                                        </td>
                                        <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                            {{$detail['totalDupSearchCount']}}件
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


        <form method="post" action="{{ route('manageUsageStatusDetailPdf', ['editId' => $companyId]) }}">
            @csrf
            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="w-1/2">
                </div>

                <div class="w-1/2 text-right">
                    <div class="inline-flex">
                        <button type="submit" formtarget="_blank"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                PDFダウンロード
                        </button>
                        <div class="w-2"></div>

                        <button type="button" onclick="location.href = '{{ route('manageUsageStatus', ['page' => $pageNo]) }}';"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            一覧に戻る
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

@endsection
