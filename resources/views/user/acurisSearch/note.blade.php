@extends((auth()->user()->type == 1) ? 'manage.layout': 'user.layout')

@section('contents')

<header class="bg-white shadow-sm">
    <div class="flex max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="w-3/12">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                海外検索画面
            </h1>
        </div>
        <div class="w-7/12">
        </div>
        <img class="w-2/12" src="/acuris_icon.jpg">
    </div>
</header>

<main>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="px-3 py-8 my-3 shadow overflow-hidden border border-gray-200 sm:rounded-lg">
                        <div class="px-4 py-2 font-normal text-2xl">
                            アキュリス検索についての注意事項<BR>
                        </div>
                        <div class="px-10 py-2 font-normal text-xl">
                            {!! nl2br(config('note.acurisSearch.note.note'))  !!}
                        </div>
                    </div>
                    <div class="py-4 text-center">
                        <button onclick="location.href = '{{ route('userAcurisSearch') }}';"
                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            上記事項を確認して検索する
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
