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
                        <form method="post" action="{{ route($routeName) }}"  enctype="multipart/form-data">
                            @csrf
                            <div class="py-5 shadow overflow-hidden border border-green-400 sm:rounded-lg bg-green-100">
                                <div class="py-3">
                                    <label class="px-10 font-medium">{{ $formTitle }}</label>
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
                                    <input class="ml-20" type="checkbox" name="fuzzyFlg" id="fuzzyFlg" name="fuzzyFlg" value='true' {{ old('fuzzyFlg', 'true') == 'true' ? 'checked="checked"' : '' }}>
                                    <span>※旧漢字・複雑漢字を検索に含めます。</span>
                                </div>
                                @if($searchType === 'registry')
                                <div class="py-3">
                                    <label class="px-10 font-medium" for="searchRepFlg">法人・代表者のみ検索</label>
                                    <input type="hidden" name="searchRepFlg" value='false'>
                                    <input class="ml-4" type="checkbox" id="searchRepFlg" name="searchRepFlg" value='true'>
                                </div>
                                <div class="py-3">
                                    <label class="px-10 font-medium" for="retireFlg">退任した役員を含めない</label>
                                    <input type="hidden" name="retireFlg" value='false'>
                                    <input class="ml-2" type="checkbox" id="retireFlg" name="retireFlg" value='true'>
                                </div>
                                @endif
                            </div>

                            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                <div class="w-1/2">
                                </div>

                            </div>
                        </form>

                        <div class="px-3 py-6 my-3 shadow overflow-hidden border border-gray-200 sm:rounded-lg">
                            {!! nl2br($notes)  !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
