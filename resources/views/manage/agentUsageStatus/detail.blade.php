@extends('manage.layout')

@section('contents')

<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-lg leading-6 font-semibold text-gray-900">
            代理店利用状況詳細画面
        </h1>
    </div>
</header>
<main>
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        @php
        $maskedName = mb_substr($companyName, 0, 2) . str_repeat('*', mb_strlen($companyName) - 3) . mb_substr($companyName, -1);
        @endphp
        会社名:{{ $maskedName }}
    </div>

    @if ($userIdList !== [])
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-500">
                                <tr>
                                    <th scope="col" class="w-200 px-5 py-3 text-left text-xs font-medium text-white border">
                                        ID
                                    </th>
                                    <th scope="col" class="w-40 px-5 py-3 text-left text-xs font-medium text-white border">
                                        検索件数
                                    </th>
                                    <th scope="col" class="w-40 px-5 py-3 text-left text-xs font-medium text-white border">
                                        同一ワード検索件数
                                    </th>
                                </tr>
                            </thead>
                            @foreach ($userIdList as $userItem)
                            @php
                            $maskedUserId = preg_replace_callback('/-(.*?)-/', function ($matches) {
                            return '-' . str_repeat('*', strlen($matches[1])) . '-';
                            }, $userItem->userId);
                            @endphp

                            <tbody class="bg-white">
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                        {{$maskedUserId}}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                        {{$userItem->searchCount}}件
                                    </td>
                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                        {{$userItem->dupSearchCount}}件
                                    </td>
                                </tr>
                            </tbody>
                            @endforeach
                            <tbody class="bg-white">
                                <tr>
                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                        {{$totalSearchCount}}件
                                    </td>
                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                        {{$totalDupSearchCount}}件
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

    <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="w-1/2">
        </div>

        <div class="w-1/2 text-right">
            <div class="inline-flex">

                <button type="button" onclick="location.href = '{{ route('manageAgentUsageStatus', ['page' => $pageNo]) }}';" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                    一覧に戻る
                </button>
            </div>
        </div>
    </div>
</main>

@endsection