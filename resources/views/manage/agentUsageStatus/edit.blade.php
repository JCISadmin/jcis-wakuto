@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                代理店登録・編集画面
            </h1>
        </div>
    </header>

    <main>
        @include('msg')
        <form id="listForm" method="post" action="{{ route('manageAgentConfirm') }}">
            @csrf
            <input type="hidden" name="distributor_cd" value="{{ $distributor_cd }}">
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
                                                代理店コード
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-normal text-white border">
                                                代理店名（会社名)
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_contractStatus"></label>
                                                <select name="agent[status]" id="agent_status"
                                                        class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
														<option value=0>契約中</option>
														<option value=1>未契約</option>
                                                </select>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_agent_cd"></label>
                                                <input type="text"  maxlength="20" name="agent[agent_cd]" id="agent_cd" value="{{ old('agent.cd', $agent['agent_cd']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_name"></label>
                                                <input type="text" size="50" maxlength="20" name="agent[name]" id="agent_name" value="{{ old('agent.name', $agent['name']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                郵便番号
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                都道府県名
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                                住所
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_postcode"></label>
                                                <input type="text" maxlength="7" name="agent[postCode]" id="agent_postCode" value="{{ old('agent.postCode', $agent['postCode']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_prefcode"></label>
												<select name="agent[prefCode]">
													<option value="1,北海道">北海道</option>
													<option value="13,東京都">東京都</option>
												</select>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_address"></label>
                                                <input type="text" size="200" maxlength="200" name="agent[address]" id="agent_address" value="{{ old('agent.address', $agent['address']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="detailTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-normal text-white border">
                                                電話番号
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                メールアドレス
                                            </th>
                                            <th scope="col" class="px-8 py-3 text-left text-xs font-medium text-white border">
                                                企業Webサイト
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_tel"></label>
                                                <input type="text" maxlength="10" name="agent[tel]" id="agent_tel" value="{{ old('agent.tel', $agent['tel']) }}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_email"></label>
                                                <input type="text" size="50" size="20" maxlength="20" name="agent[mailCompanyName]" id="agent_mailCompanyName" value="{{ old('agent.mailCompanyName', $agent['mailCompanyName']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-8 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="agent_homepageurl"></label>
                                                <input type="text" size="50" maxlength="100" name="agent[homePageUrl]" id="agent_homepageurl" value="{{ old('agent.homePageUrl', $agent['homePageUrl']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

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
                        <div class="w-2"></div>

                        <button type="submit" onclick="btnAction('update')"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            更新
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </main>

    @php
        /* @var  $addDisabled */
        $addDisabled = '';
    @endphp

    <table id="addIpLine" class="hidden">
        <tbody>
            <tr id="ipRow">
                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                    <input type="text" maxlength="15"
                    class="ipInput px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-medium border">
                    <button type="button" id="btnIpDel"
                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400">
                        削除
                    </button>
                </td>
            </tr>
        </tbody>
    </table>


    <script>
    </script>

@endsection
