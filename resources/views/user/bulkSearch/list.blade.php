@extends('user.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                一括検索一覧画面
            </h1>
        </div>
    </header>

    <main>

        @include('msg')

        <div class="max-w-7xl text-right mx-auto py-3 sm:px-6 lg:px-8">

            <button onclick="location.reload();"
            class="w-20 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
            更新
            </button>

            <button onclick="location.href = '{{ route('userBulkSearchAdd') }}';"
            class="w-20 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
            新規追加
            </button>

        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            登録日時
                                        </th>
                                        <th scope="col" class="w-25 px-3 py-3 text-left text-xs font-medium text-white border">
                                            種類
                                        </th>
                                        <th scope="col" class="w-80 px-3 py-3 text-left text-xs font-medium text-white border">
                                            登録名
                                        </th>
                                        <th scope="col" class="w-25 px-3 py-3 text-left text-xs font-medium text-white border">
                                            状態
                                        </th>
                                        <th scope="col" class="w-60 px-3 py-3 text-left text-xs font-medium text-white border">
                                            ダウンロード
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($dataList as $item)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium border">
                                                {{ date_format(new Datetime($item->createDatetime), 'Y/m/d') }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->result }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium border">
                                                @php
                                                    /** @var  $item */
                                                    /** @var  $jsonData */
                                                    $jsonData = json_decode($item->searchCondition);
                                                @endphp
                                                {{ $jsonData->uploadName }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-center font-medium border">
                                                {{ $item->result }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-center font-medium border">
                                                <button onclick="location.reload();"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                PDF
                                                </button>
                                                <button onclick="location.href = '{{ route('userBulkSearchAdd') }}';"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                CSV
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex max-w-7xl py-6 sm:px-6 lg:px-8">
                <div class="w-5/6">
                    {{ $dataList->links('paginate') }}
                </div>
            </div>

        </div>


    </main>

@endsection
