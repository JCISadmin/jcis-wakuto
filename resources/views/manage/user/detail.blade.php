@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                ユーザー詳細画面
            </h1>
        </div>
    </header>

    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-0">
            <div class="text-right">
                <button type="button" id="btnSearch" onclick="location.href = '';"
                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        月別検索数
                </button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            状況
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            当社窓口
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-normal text-white border">
                                            当社窓口E-MAIL
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            会社名
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-normal text-white border">
                                            会社ID
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            郵便番号
                                        </th>
                                        <th scope="col" class="px-8 py-3 text-left text-xs font-medium text-white border">
                                            会社住所
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            代表電話番号
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">    
                                        
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-8 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table id="detailTable2" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            担当者名
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            担当者部署・役職
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            担当者電話番号
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            担当者E-Mail
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                    </tr>
                                </tbody>

                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            請求者名
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            請求者部署・役職
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            請求者電話番号
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            請求先TO
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            請求先CC
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                WEB検索契約
            </h1>
        </div>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="webTable1" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                        契約プラン
                                    </th>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                        契約形態
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                        トライアル開始日
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                        利用開始日
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                        利用更新日
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                        利用終了通知日
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                        利用終了予定日
                                    </th>
                                </thead>
                                
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                <div class="flex flex-col">
                                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                <table id="webTable2" class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-green-500">
                                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                            ID個数
                                                        </th>
                                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                            ID代
                                                        </th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                                            検索単価
                                                        </th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                                            年検索数
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                            デポジット残高
                                                        </th>
                                                    </thead>
                                                    
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <tr>
                                                            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                            
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                <div class="flex flex-col">
                                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                <table id="webTable3" class="min-w-full divide-y divide-gray-200">
                                                    
                                                    <thead class="bg-green-500">
                                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                            No
                                                        </th>
                                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                            ステータス
                                                        </th>
                                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                            ユーザーID
                                                        </th>
                                                        <th scope="col" colspan="2" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                                            パスワード
                                                        </th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                                            ID保有者名
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                            ID保有者部署・役職
                                                        </th>
                                                        <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                            ID保有者E-mail
                                                        </th>
                                                    </thead>

                                                    <tbody class="bg-white divide-y divide-gray-200">

                                                        <tr>
                                                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                              
                                                            </td>
                                                            <p class="border">
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border-none">
                                                                
                                                                </td>
                                                                <td class="px-1 py-4 whitespace-nowrap text-center text-sm font-medium border-none">
                                                                    <button type="button" onclick="location.href = '';"
                                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                        変更
                                                                    </button>
                                                                </td>
                                                            </p>
                                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                            
                                                            </td>
                                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                            
                                                            </td>
                                                        </tr>
                                                    
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="w-1/2">
            </div>

            <div class="w-1/2 text-right">
                <div class="inline-flex">
                    <button type="button" onclick="location.href = '';"
                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        一覧に戻る
                    </button>

                    <div class="w-2"></div>

                    <button type="submit"
                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        編集
                    </button>
                </div>
            </div>
        </div>
    </main>

@endsection
