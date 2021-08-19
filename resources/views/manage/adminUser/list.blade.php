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
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Replace with your content -->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white">
                                        No
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white">
                                        ステータス
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white">
                                        管理者ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white">
                                        パスワード
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white">
                                        管理者名
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white">
                                        管理者E-mail
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white">
                                        登録日
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        No
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        ステータス
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        管理者ID
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        パスワード
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        管理者名
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        管理者E-mail
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        登録日
                                    </td>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /End replace -->
        </div>
    </main>

@endsection
