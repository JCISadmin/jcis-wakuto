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

        @include('msg')
        
        <div class="max-w-7xl mx-auto py-3 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageUserSearchSearchReport', ['editId' => $companyId]) }}">
                @csrf
                <div class="flex flex-row pb-6">
                    <div>
                        <label class="px-2">
                            <input type="radio" name="dispType" value="all" {{ $dispType === 'all' ? 'checked="checked"' : '' }}>
                            全件
                        </label>
                        <label class="px-2">
                            <input type="radio" name="dispType" value="month" {{ $dispType === 'month' ? 'checked="checked"' : '' }}>
                                月別
                            <input type="month" value="{{ $useMonth }}" name="useMonth" id="useMonth"
                               class="mx-2 px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </label>
                        <button type="submit"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            表示
                        </button>
                    </div>
                </div>
            </form>
        </div>

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
                                            利用年月
                                        </th>
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
                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                            同一ワード検索数
                                        </th>
                                    </tr>
                                </thead>
                                @foreach($detail['year'] as $year => $yearItem)
                                    <tbody class="bg-white">
                                        <tr>
                                            <td class="border-0 px-4 py-6 whitespace-nowrap text-left text-sm font-medium">
                                                {{ $year }}年
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$yearItem['totalSearchCount']}}件
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                {{$yearItem['totalSearchPrice']}}円
                                            </td>
                                            <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                0件
                                            </td>
                                        </tr>
                                    </tbody>
                                    @foreach ($detail['month'] as $month => $monthItem)
                                        <tbody class="bg-white">
                                            <tr>
                                                <td class="border-0 px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                                                    {{date_format(new DateTime($month), 'Y年n月')}}
                                                </td>
                                                <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                </td>
                                                <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                    {{$monthItem['totalSearchCount']}}件
                                                </td>
                                                <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                    {{$monthItem['totalSearchPrice']}}円
                                                </td>
                                                <td class="border-0 px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                    0件
                                                </td>
                                            </tr>
                                            @foreach ($monthItem['report'] as $userItem)
                                                <tr>
                                                    <td class="border-0 whitespace-nowrap text-center text-sm font-medium">
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                                        {{$userItem['user']}}
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        {{$userItem['unitPrice']}}円
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        {{$userItem['count']}}件
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        {{$userItem['price']}}円
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium border">
                                                        0件
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
        @endif

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
    </main>

    <script>

        $(function(){
            let dispType = $('input[name="dispType"]:checked').val();
            
            if(dispType === 'all'){
                $('#useMonth').addClass('bg-gray-200');
                $('#useMonth').attr('readonly',true);
            }
        });

        $('input[name="dispType"]').change(function(){
            let dispType = $(this).val();
            let monthInput = $('#useMonth').val();

            if(dispType === 'all'){
                $('#useMonth').addClass('bg-gray-200');
                $('#useMonth').attr('readonly',true);
            }else if(dispType === 'month'){
                $('#useMonth').removeClass('bg-gray-200');
                $('#useMonth').attr('readonly',false);
            }
        });



    </script>







@endsection
