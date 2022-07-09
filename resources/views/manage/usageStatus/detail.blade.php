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
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            会社名:{{ $companyName }}
        </div>

        @include('msg')
        
        @if ($detail !== null)
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
                                            検索数
                                        </th>
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            金額
                                        </th>
                                    </tr>
                                </thead>
                                @foreach ($detail['report'] as $userItem)
                                    <tbody class="bg-white">
                                        <tr>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                {{$userItem['user']}}
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                {{$userItem['unitPrice']}}円
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$userItem['count']}}件
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$userItem['price']}}円
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


        <form method="post" action="{{ route('manageUsageStatusPdf', ['editId' => $companyId]) }}">
            @csrf
            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="w-1/2">
                </div>

                <div class="w-1/2 text-right">
                    <div class="inline-flex">
                        <button type="button" onclick="location.href = '{{ route('manageUsageStatusCsv', ['editId' => $companyId]) }}';"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            CSVダウンロード
                        </button>
                        <div class="w-2"></div>

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
