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
                                            <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="subject"><span class="text-white">今月検索件数</span></label>
                                            </td>
                                            <td class="w-1/6 px-3 py-3 whitespace-nowrap text-sm font-medium border border-r-0 text-right">
                                                <span>{{number_format($useReportList['monthSearchCount'])}}</span>
                                            </td>
                                            <td class="w-4/6 px-3 py-3 whitespace-nowrap text-sm font-medium border border-l-0 text-left">
                                                <span>件</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="name"><span class="text-white">年間検索件数</span></label>
                                            </td>
                                            <td class="w-1/6 px-3 py-3 whitespace-nowrap text-sm font-medium border border-r-0 text-right">
                                                <span>{{number_format($useReportList['yearSearchCount'])}}</span>
                                            </td>
                                            <td class="w-4/6 px-3 py-3 whitespace-nowrap text-sm font-medium border border-l-0 text-left">
                                                <span>件</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/6 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="mail"><span class="text-white">デポジット残高</span></label>
                                            </td>
                                            <td class="w-1/6 px-3 py-3 whitespace-nowrap text-sm font-medium border border-r-0 text-right">
                                                <span>{{number_format($useReportList['depositBalance'])}}</span>
                                            </td>
                                            <td class="w-4/6 px-3 py-3 whitespace-nowrap text-sm font-medium border border-l-0 text-left">
                                                <span>円</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                <div class="w-1/2">
                                </div>

                                <div class="w-1/2 text-right">
                                    <div class="inline-flex">
                                        <button type="button" onclick="location.href = '{{ route('printUseReport') }}';"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            PDF保存
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </form>
        </div>
    </main>
@endsection
