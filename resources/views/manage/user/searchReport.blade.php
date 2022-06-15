@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                月別検索数
            </h1>
        </div>
    </header>

    <main>
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            会社名:{{ $companyName }}
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                請求年月
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                ID/担当者名
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                検索数
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                同一ワード検索数
                                            </th>
                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                金額
                                            </th>
                                        </tr>
                                    </thead>

                                    @foreach($detail as $yearItem)

                                        <tbody class="bg-white">
                                                <tr>
                                                    <td class="border-0 px-4 py-6 whitespace-nowrap text-left text-sm font-medium">
                                                        {{ $yearItem['year'] }}年
                                                    </td>
                                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        {{$yearItem['yearTotalCount']}}件
                                                    </td>
                                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        0件
                                                    </td>
                                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        {{$yearItem['yearTotalCount'] * 100}}円
                                                    </td>

                                                </tr>

                                                @foreach($yearItem['userInfo'] as $yearValue)
                                                    <tr>
                                                        <td class="border-0 whitespace-nowrap text-center text-sm font-medium">
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                                            {{$yearValue['user']}}
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                            {{$yearValue['count']}}件
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                            0件
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                            {{$yearValue['count'] * 100}}円
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </tbody>

                                        @foreach ($yearItem['monthList'] as $item)
                                            
                                            <tbody class="bg-white">
                                                <tr>
                                                    <td class="border-0 px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                                                        {{date_format(new DateTime($item['month']), 'Y年n月')}}
                                                    </td>
                                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        {{$item['monthTotalCount']}}件
                                                    </td>
                                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        0件
                                                    </td>
                                                    <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        {{$item['monthTotalCount'] * 100}}円
                                                    </td>

                                                </tr>

                                                @foreach($item['userInfo'] as $value)
                                                    @php
                                                        /* @var  $item */
                                                    @endphp
                                                    <tr>
                                                        <td class="border-0 whitespace-nowrap text-center text-sm font-medium">
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                                            {{$value['user']}}
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                            {{$value['count']}}件
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                            0件
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                            {{$value['count'] * 100}}円
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>






        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-5">
            <div class="text-right">
                <button type="button" onclick="location.href = '{{ route('manageUserDetail', ['editId' => $companyId]) }}';"
                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                    詳細に戻る
                </button>
                <button type="button" onclick="location.href = '{{ route( 'manageUserSearchReportPdf', ['editId' => $companyId]) }}';"
                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        PDFダウンロード
                </button>
            </div>
        </div>


@endsection
