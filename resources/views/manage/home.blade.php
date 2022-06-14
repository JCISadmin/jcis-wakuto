@extends('manage.layout')

@section('contents')
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                管理画面ホーム
            </h1>
        </div>
    </header>

    <main>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

            <div class="grid grid-cols-2 gap-6">
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('manageUser') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        ユーザー一覧画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" type="button" onclick="location.href = '{{ route('manageClaim') }}';"
                        class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        請求一覧画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('userSearch') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        検索画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('manageDataRegister') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        データ一括登録画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('userCsvBulkSearch') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        一括検索画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('manageDataEdit') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        データ登録変更画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('userRegistryBulkSearch') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        登記情報検索画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('manageConvertFont') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        旧字体変換マスタ画面
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="location.href = '{{ route('manageAdminUser') }}';"
                            class="w-4/6 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        管理ユーザー一覧画面
                    </button>
                </div>
            </div>

        </div>
    </main>

@endsection
