@extends('user.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                一括検索アップロード確認
            </h1>
        </div>
    </header>

    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('userContactSend') }}">
                @csrf

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
                                                <label for="name"><span class="text-white">検索対象ダウンロード</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            <div class="inline-flex">
                                                @if($isDl)
                                                    <button type="submit"
                                                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                        ダウンロード
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </main>

    

@endsection