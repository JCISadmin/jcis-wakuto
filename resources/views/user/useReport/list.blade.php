@extends('user.layout')

@section('contents')
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                利用明細画面
            </h1>
        </div>
    </header>

    <main>
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            会社名:{{ $companyName }}
        </div>

        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="text-right">{{$date}}</div>
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                <tbody>
                                    <tr>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">今月検索件数</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-sm font-medium border border-r-0 text-right">
                                            <span>{{number_format($monthSearchCount)}}</span>
                                        </td>
                                        <td class="w-4/6 px-3 py-5 whitespace-nowrap text-sm font-medium border border-l-0 text-left">
                                            <span>件</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-5 whitespace-nowrap text-sm font-medium border">
                                            <label for="name"><span class="text-white">年間検索件数</span></label>
                                        </td>
                                        <td class="w-1/6 px-3 py-5 whitespace-nowrap text-sm font-medium border border-r-0 text-right">
                                            <span>{{number_format($yearSearchCount)}}</span>
                                        </td>
                                        <td class="w-4/6 px-3 py-5 whitespace-nowrap text-sm font-medium border border-l-0 text-left">
                                            <span>件</span>
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
            <div class="flex">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="webTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="w-48 px-3 py-3 text-left text-xs font-medium text-white border">
                                            WEBデポジット残高
                                        </th>
                                        <th scope="col" class="w-48 px-3 py-3 text-left text-xs font-medium text-white border">
                                            WEB検索可残数
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $webDeposit }}円
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $webRemainCount }}件
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
            <div class="flex">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="webTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="w-48 px-3 py-3 text-left text-xs font-medium text-white border">
                                            APIデポジット残高
                                        </th>
                                        <th scope="col" class="w-48 px-3 py-3 text-left text-xs font-medium text-white border">
                                            API検索可残数
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $apiDeposit }}円
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $apiRemainCount }}件
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
            <form method="post" action="{{ route('manageUserSearchSearchReport') }}">
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

        @if ($depositList['web'] !== [])
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                システム検索
            </h1>
        </div>
        <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
            <div class="flex">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="w-48 px-5 py-3 text-left text-xs font-medium text-white border">
                                            デポジット検索単価
                                        </th>
                                        <th scope="col" class="w-48 px-5 py-3 text-left text-xs font-medium text-white border">
                                            デポジット不足検索数
                                        </th>
                                        <th scope="col" class="w-48 px-5 py-3 text-left text-xs font-medium text-white border">
                                            デポジット不足金額
                                        </th>
                                    </tr>
                                </thead>
                                
                                @foreach ($depositList['web'] as $webDepositItem)
                                <tbody>
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                            {{ $webDepositItem['unitPrice'] }}円
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                            {{ $webDepositItem['count'] }}件
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                            {{ $webDepositItem['price'] }}円
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

        @if ($depositList['api'] !== [])
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                API検索
            </h1>
        </div>
        <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
            <div class="flex">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="w-48 px-5 py-3 text-left text-xs font-medium text-white border">
                                            デポジット検索単価
                                        </th>
                                        <th scope="col" class="w-48 px-5 py-3 text-left text-xs font-medium text-white border">
                                            デポジット不足検索数
                                        </th>
                                        <th scope="col" class="w-48 px-5 py-3 text-left text-xs font-medium text-white border">
                                            デポジット不足金額
                                        </th>
                                    </tr>
                                </thead>
                                
                                @foreach ($depositList['api'] as $apiDepositItem)
                                <tbody>
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                            {{ $apiDepositItem['unitPrice'] }}円
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                            {{ $apiDepositItem['count'] }}件
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium border">
                                            {{ $apiDepositItem['price'] }}円
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
                                    @if($dispType === 'all')
                                    <tbody class="bg-white">
                                        <tr>
                                            <td class="border-0 px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
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
                                                {{$yearItem['totalDupSearchCount']}}件
                                            </td>
                                        </tr>
                                    </tbody>
                                    @endif
                                    @foreach ($detail['month'][$year] as $month => $monthItem)
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
                                                    {{$monthItem['totalDupSearchCount']}}件
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
                                                        {{$userItem['dupCount']}}件
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


        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
            <p>※算出件数はVer.3リリース後の件数になります。</p>
            <p>　システム切り替え以前の件数は含んでおりませんのでご注意ください。</p>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-5">
            <div class="text-right">
                <button type="button" onclick="location.href = '{{ route('printUseReport') }}';"
                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                    PDF保存
                </button>
            </div>
        </div>
    </main>
@endsection
