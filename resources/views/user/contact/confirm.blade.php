@extends('user.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                お問い合わせ確認画面
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
                                                <label for="subject"><span class="text-white">件名</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                {{ $item['subject'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="name"><span class="text-white">お名前</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                {{ $item['name'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="mail"><span class="text-white">メールアドレス</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                {{ $item['mail'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="companyName"><span class="text-white">会社名</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                {{ $item['companyName'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="departmentJob"><span class="text-white">部署・役職</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                {{ $item['departmentJob'] }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="contactDetail"><span class="text-white">お問い合わせ内容</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                {!! nl2br(e($item['contactDetail'])) !!}
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
                                        <button type="button" onclick="location.href = '{{ route('userContact') }}';"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            戻る
                                        </button>

                                        <div class="w-2"></div>

                                        <button type="submit"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            送信
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
