@extends('user.layout')

@section('contents')
<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-lg leading-6 font-semibold text-gray-900">
            検索確認画面
        </h1>
    </div>
</header>

<main>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="py-4"></div>

        <div class="mx-80 border-b-2 shadow-sm">
            <div class="justify-center flex">
                <div class="text-xl font-bold mr-96 flex-initial px-4">
                    INFO
                </div>
            </div>
            <div class="py-2"></div>
            <div class="justify-center flex">
                <div class="text-xl flex-initial px-4">
                    デポジット残高が不足している可能性があります。<BR>
                    検索を行いますか？
                </div>
            </div>
            <div class="py-4"></div>
        </div>

        <div class="py-4"></div>

        <div class="flex justify-center max-w-full mx-auto py-4 sm:px-6 lg:px-8">
            <button type="button" onclick="location.href = '../search';"
                    class="m-2 px-12 py-2 border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                キャンセル
            </button>
            <button type="button" onclick="location.href = './confirm';"
                    class="m-2 px-12 py-2 border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                検索する
            </button>
        </div>
    </div>
</main>
@endsection