@extends((auth()->user()->type == 1) ? 'manage.layout': 'user.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                {{ $title }}
            </h1>
        </div>
    </header>

    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                <tbody>
                                    <tr>
                                        <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">検索件数</span></label>
                                        </td>
                                        <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            {{ $rawCnt }} 件
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            <label for="subject"><span class="text-white">あいまい検索</span></label>
                                        </td>
                                        <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            @if($fuzzyFlg === 'true')
                                                〇
                                            @endif
                                        </td>
                                    </tr>

                                    @if($dlFlg)
                                    <tr>
                                        <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            <label for="name"><span class="text-white">検索対象ダウンロード</span></label>
                                        </td>
                                        <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            <div class="inline-flex">
                                                <button type="button" onclick="location.href='{{ route('userRegistryBulkSearchDownload') }}';"
                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    ダウンロード
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if($dlFlg)
                        <form method="post" action="{{ route('userRegistryBulkSearchReUpload') }}"  enctype="multipart/form-data" class="py-5">
                            @csrf
                            <div class="py-5 shadow overflow-hidden border border-green-400 sm:rounded-lg bg-green-100">
                                <div class="py-3">
                                    <label class="px-10 font-medium">検索用CSVデータ</label>
                                    <input type="file" name="bulk_file" class="w-1/2 px-6 py-2 justify-center border border-green-400 rounded-md shadow-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                                        <div class="inline-flex px-3">
                                            <button type="submit"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                アップロード
                                            </button>
                                        </div>
                                </div>
                                <div class="py-3">
                                    <label class="px-10 font-medium" for="fuzzyFlg">あいまい検索</label>
                                    <input type="hidden" name="fuzzyFlg" value='false'>
                                    <input class="ml-20" type="checkbox" name="fuzzyFlg" id="fuzzyFlg" name="fuzzyFlg" value='true' {{ old('fuzzyFlg', $fuzzyFlg) == 'true' ? 'checked="checked"' : '' }}>
                                    <span>※旧漢字・複雑漢字を検索に含めます。</span>
                                </div>
                            </div>
                        </form>
                        @endif

                        <div class="px-3 py-6 my-3 shadow overflow-hidden border border-gray-200 sm:rounded-lg">
                            {!! nl2br($notes)  !!}
                        </div>

                        <div class="max-w-7xl text-center mx-auto py-3 sm:px-6 lg:px-8">
                            <button onclick="location.href = '{{ route($routeNameAdd) }}';"
                            class="px-3 py-2 mx-3 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                アップロード画面へ戻る
                            </button>

                            <button onclick="location.href = '{{ route($routeNameSearch) }}';"
                            class="px-3 py-2 mx-3 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                一括検索
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
