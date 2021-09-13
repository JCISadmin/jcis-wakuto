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



        <button onclick="location.href = '{{ route('userBulkSearchAdd') }}';"
            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
            新規追加
        </button>

    </main>

@endsection