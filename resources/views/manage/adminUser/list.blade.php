@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                管理ユーザー一覧画面
            </h1>
        </div>
    </header>
    <main>

        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageAdminUserSearch') }}">
                {{ csrf_field() }}
                <div class="flex">
                    <div class="flex-initial px-4">
                        <label for="userId">管理者ID</label>
                        <input type="text" value="{{ $userId }}" name="userId" id="userId" class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="flex-initial px4">
                        <label for="userName">管理者名</label>
                        <input type="text" value="{{ $userName }}" name="userName" id="userName" class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="flex-initial px-4">
                        <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            検索
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Replace with your content -->
            <form method="post" action="{{ route('manageAdminUserUpdate') }}">
                {{ csrf_field() }}
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="border px-2 py-3 text-left text-xs font-medium text-white">
                                            No
                                        </th>
                                        <th scope="col" class="border px-6 py-3 text-left text-xs font-medium text-white">
                                            ステータス
                                        </th>
                                        <th scope="col" class="border px-6 py-3 text-left text-xs font-medium text-white">
                                            管理者ID
                                        </th>
                                        <th scope="col" class="border px-6 py-3 text-left text-xs font-medium text-white">
                                            パスワード
                                        </th>
                                        <th scope="col" class="border px-6 py-3 text-left text-xs font-medium text-white">
                                            管理者名
                                        </th>
                                        <th scope="col" class="border px-6 py-3 text-left text-xs font-medium text-white">
                                            管理者E-mail
                                        </th>
                                        <th scope="col" class="border px-6 py-3 text-left text-xs font-medium text-white">
                                            登録日
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($userList as $item)
                                            @php
                                                $num = $userList->firstItem() + $loop->index;
                                            @endphp
                                            <tr>
                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border">
                                                    {{ $num }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                    ステータス
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                    <input type="text" name="userInfo[{{ $num }}][userId]" id="userId_{{ $num }}" value="{{ old(sprintf('userInfo.%d.userId', $num), $item->userId) }}"
                                                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                    {{ $item->password }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                    <input type="text" name="userInfo[{{ $num }}][userName]" id="userName_{{ $num }}" value="{{ old(sprintf('userInfo.%d.userName', $num), $item->userName) }}"
                                                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">

                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                    <input type="text" name="userInfo[{{ $num }}][mail]" id="mail_{{ $num }}" value="{{ old(sprintf('userInfo.%d.mail', $num), $item->mail) }}"
                                                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                    <input type="date" name="userInfo[{{ $num }}][createDatetime]" id="createDatetime_{{ $num }}" value="{{ old(sprintf('userInfo.%d.createDatetime', $num), $item->createDatetime) }}"
                                                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div class="w-5/6">
                        {{ $userList->links('paginate') }}
                    </div>

                    <div class="w-1/6 text-right">
                        <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            更新
                        </button>
                    </div>
                </div>

            </form>
            <!-- /End replace -->
        </div>
    </main>

@endsection
