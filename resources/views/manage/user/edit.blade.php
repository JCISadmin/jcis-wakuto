@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                ユーザー編集画面
            </h1>
        </div>
    </header>

    <main>
        @include('msg')
        <form method="post" action="{{ route('manageUserUpdate') }}">
            @csrf
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
                                                <label for="userCompany_contractStatus"></label>
                                                <select name="userCompany[contractStatus]" id="userCompany_contractStatus"
                                                        class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    @foreach($selectList['contractStatus'] as $item)
                                                        <option value="{{ $item->contractStatus }}" {{ $item->contractStatus == $userDetailList['userCompany']['contractStatus'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_chargeName"></label>
                                                <input type="text" maxlength="20" name="userCompany[chargeName]" id="userCompany_chargeName" value="{{ old('userCompany.chargeName', $userDetailList['userCompany']['chargeName']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_chargeMail"></label>
                                                <input type="text" name="userCompany[chargeMail]" id="userCompany_chargeMail" value="{{ old('userCompany.chargeMail', $userDetailList['userCompany']['chargeMail']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_name"></label>
                                                <input type="text" maxlength="40" name="userCompany[name]" id="userCompany_name" value="{{ old('userCompany.name', $userDetailList['userCompany']['name']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_companyId"></label>
                                                <input type="text" maxlength="5" name="userCompany[companyId]" id="userCompany_companyId" value="{{ old('userCompany.companyId', $userDetailList['userCompany']['companyId']) }}"
                                                        {{ $editId == '' ? '' : 'readonly' }}
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_postCode"></label>
                                                <input type="text" maxlength="7" name="userCompany[postCode]" id="userCompany_postCode" value="{{ old('userCompany.postCode', $userDetailList['userCompany']['postCode']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-8 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_address"></label>
                                                <input type="text" maxlength="50" name="userCompany[address]" id="userCompany_address" value="{{ old('userCompany.address', $userDetailList['userCompany']['address']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_tel"></label>
                                                <input type="text" maxlength="20" name="userCompany[tel]" id="userCompany_tel" value="{{ old('userCompany.tel', $userDetailList['userCompany']['tel']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table id="userCompany_detailTable2" class="min-w-full divide-y divide-gray-200">
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
                                                <label for="userCompany_staffName"></label>
                                                <input type="text" maxlength="20" name="userCompany[staffName]" id="userCompany_staffName" value="{{ old('userCompany.staffName', $userDetailList['userCompany']['staffName']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_staffDepartmentJob"></label>
                                                <input type="text" maxlength="20" name="userCompany[staffDepartmentJob]" id="userCompany_staffDepartmentJob" value="{{ old('userCompany.staffDepartmentJob', $userDetailList['userCompany']['staffDepartmentJob']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_staffTel"></label>
                                                <input type="text" maxlength="20" name="userCompany[staffTel]" id="userCompany_staffTel" value="{{ old('userCompany.staffTel', $userDetailList['userCompany']['staffTel']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_staffMail"></label>
                                                <input type="text" name="userCompany[staffMail]" id="userCompany_staffMail" value="{{ old('userCompany.staffMail', $userDetailList['userCompany']['staffMail']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
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
                                                <label for="userCompany_claimName"></label>
                                                <input type="text" maxlength="20" name="userCompany[claimName]" id="userCompany_claimName" value="{{ old('userCompany.claimName', $userDetailList['userCompany']['claimName']) }}"
                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_claimDepartmentJob"></label>
                                                <input type="text" maxlength="20" name="userCompany[claimDepartmentJob]" id="userCompany_claimDepartmentJob" value="{{ old('userCompany.claimDepartmentJob', $userDetailList['userCompany']['claimDepartmentJob']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_claimTel"></label>
                                                <input type="text" maxlength="20" name="userCompany[claimTel]" id="userCompany_claimTel" value="{{ old('userCompany.claimTel', $userDetailList['userCompany']['claimTel']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_claimMailTo"></label>
                                                <input type="text" name="userCompany[claimMailTo]" id="userCompany_claimMailTo" value="{{ old('userCompany.claimMailTo', $userDetailList['userCompany']['claimMailTo']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_claimMailCc"></label>
                                                <input type="text" name="userCompany[claimMailCc]" id="userCompany_claimMailCc" value="{{ old('userCompany.claimMailCc', $userDetailList['userCompany']['claimMailCc']) }}"
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
                                        <tr>
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
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_contractPlanId"></label>
                                                <select name="web[contractPlanId]" id="web_contractPlanId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    <option value="" {{ '' == $userDetailList['contractPlan']['web']['contractPlanId'] ? 'selected' : '' }}>契約なし</option>
                                                    @foreach($selectList['contractPlan'] as $item)
                                                        <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId == $userDetailList['contractPlan']['web']['contractPlanId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_contractTypeId"></label>
                                                <select name="web[contractTypeId]" id="web_contractTypeId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}" {{ $item->contractTypeId == $userDetailList['contractPlan']['web']['contractTypeId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_startTrial"></label>
                                                <input type="date" name="web[startTrial]" id="web_startTrial"
                                                        value="{{ old('web.startTrial', '' == $userDetailList['contractPlan']['web']['startTrial'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['startTrial']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useStartDate"></label>
                                                <input type="date" name="web[useStartDate]" id="web_useStartDate"
                                                        value="{{ old('web.useStartDate', '' == $userDetailList['contractPlan']['web']['useStartDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useStartDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useUpdateDate"></label>
                                                <input type="date" name="web[useUpdateDate]" id="web_useUpdateDate"
                                                        value="{{ old('web.useUpdateDate', '' == $userDetailList['contractPlan']['web']['useUpdateDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useUpdateDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useEndAlertDate"></label>
                                                <input type="date" name="web[useEndAlertDate]" id="web_useEndAlertDate"
                                                        value="{{ old('web.useEndAlertDate', '' == $userDetailList['contractPlan']['web']['useEndAlertDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndAlertDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useEndDate"></label>
                                                <input type="date" name="web[useEndDate]" id="web_useEndDate"
                                                        value="{{ old('web.useEndDate', '' == $userDetailList['contractPlan']['web']['useEndDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
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
                                                            <tr>
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
                                                            </tr>
                                                        </thead>

                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_ids"></label>
                                                                    <input type="hidden" name="web[ids]" id="web_ids" value="{{ old('web.ids', $userDetailList['contractPlan']['web']['ids']) }}">
                                                                    {{ $userDetailList['contractPlan']['web']['ids'] }}
                                                                </td>
                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_idUnitPrice"></label>
                                                                    <input type="text" maxlength="10" name="web[idUnitPrice]" id="web_idUnitPrice" value="{{ old('web.idUnitPrice', $userDetailList['contractPlan']['web']['idUnitPrice']) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_searchUnitPrice"></label>
                                                                    <input type="text" maxlength="3" name="web[searchUnitPrice]" id="web_searchUnitPrice" value="{{ old('web.searchUnitPrice', $userDetailList['contractPlan']['web']['searchUnitPrice']) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_searchCount"></label>
                                                                    <input type="text" name="web[searchCount]" id="web_searchCount" value="{{ old('web.searchCount', $userDetailList['contractPlan']['web']['searchCount']) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_deposit"></label>
                                                                    <input type="text" maxlength="10" name="web[deposit]" id="web_deposit" value="{{ old('web.deposit', $userDetailList['contractPlan']['web']['deposit']) }}"
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
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-0">
                                    <div class="text-right">
                                        <button type="button" id="btnWebAdd"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                追加
                                        </button>
                                    </div>
                                </div>
                                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                    <div class="flex flex-col">
                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                    <input type="hidden" name="webNum" id="webNum"
                                                           value="{{ old('webNum', count($userDetailList['contractPlan']['web']['userDetail'])) }}">
                                                    <table id="webTable3" class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-green-500">
                                                            <tr>
                                                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                                    No
                                                                </th>
                                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                                    ステータス
                                                                </th>
                                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                                    ユーザーID
                                                                </th>
                                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
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
                                                            </tr>
                                                        </thead>

                                                        <tbody class="bg-white divide-y divide-gray-200">

                                                        @php
                                                            $num = 0;
                                                        @endphp

                                                        @foreach( $userDetailList['contractPlan']['web']['userDetail'] as $item)
                                                            @php
                                                                /* @var  $num */
                                                                /* @var  $userDetailList */
                                                                /* @var  $loop */
                                                                $num =   $loop->index + 1;
                                                            @endphp
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $num }}
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <select name="web[userDetail][{{ $num }}][delFlg]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                            <option value="0" {{ $item['delFlg'] == 0 ? 'selected' : '' }}>有効</option>
                                                                            <option value="1" {{ $item['delFlg'] == 1 ? 'selected' : '' }}>無効</option>
                                                                        </select>
                                                                    </label>
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <input type="hidden" name="web[userDetail][{{ $num }}][userId]" id="web_userId_{{ $num }}" value="{{ $item['userId'] }}">
                                                                    {{$item['userId']}}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                    <input type="hidden" name="web[userDetail][{{ $num }}][password]" id="web_password_{{ $num }}" value="{{ $item['password'] }}">
                                                                    {{$item['password']}}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="web[userDetail][{{ $num }}][name]" id="web_name_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.name', $num), $item['name']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="web[userDetail][{{ $num }}][departmentJob]" id="web_departmentJob_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.departmentJob', $num), $item['departmentJob']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="web[userDetail][{{ $num }}][mail]" id="web_mail_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.mail', $num), $item['mail']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                            </tr>

                                                        @endforeach

                                                        @for ($i = 0; $i < old('webNum', 0) - $num; $i++)
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <select name="addWebDelFlg[]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                            <option value="0" {{ old('addWebDelFlg.' . $i) == 0 ? 'selected' : '' }}>有効</option>
                                                                            <option value="1" {{ old('addWebDelFlg.' . $i) == 1 ? 'selected' : '' }}>無効</option>
                                                                        </select>
                                                                    </label>
                                                                </td>
                                                                <td class="user px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <input type="hidden" name="addWebUserId[]" value="{{ old('addWebUserId.' . $i) }}">
                                                                    {{ old('addWebUserId.' . $i) }}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="addWebName[]" value="{{ old('addWebName.' . $i) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="addWebDepartmentJob[]" value="{{ old('addWebDepartmentJob.' . $i) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="addWebDepartmentJobMail[]" value="{{ old('addWebDepartmentJobMail.' . $i) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        @endfor

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

            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <h1 class="text-lg leading-6 font-semibold text-gray-900">
                    API検索契約
                </h1>
            </div>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
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
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_contractPlanId"></label>
                                                <select name="api[contractPlanId]" id="api_contractPlanId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    <option value="" {{ '' == $userDetailList['contractPlan']['api']['contractPlanId'] ? 'selected' : '' }}>契約なし</option>
                                                    @foreach($selectList['contractPlan'] as $item)
                                                        <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId == $userDetailList['contractPlan']['api']['contractPlanId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_contractTypeId"></label>
                                                <select name="api[contractTypeId]" id="api_contractTypeId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}" {{ $item->contractTypeId == $userDetailList['contractPlan']['api']['contractTypeId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_startTrial"></label>
                                                <input type="date" name="api[startTrial]" id="api_startTrial"
                                                        value="{{ old('api.startTrial', '' == $userDetailList['contractPlan']['api']['startTrial'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['startTrial']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useStartDate"></label>
                                                <input type="date" name="api[useStartDate]" id="api_useStartDate"
                                                        value="{{ old('api.useStartDate', '' == $userDetailList['contractPlan']['api']['useStartDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useStartDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useUpdateDate"></label>
                                                <input type="date" name="api[useUpdateDate]" id="api_useUpdateDate"
                                                        value="{{ old('api.useUpdateDate', '' == $userDetailList['contractPlan']['api']['useUpdateDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useUpdateDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useEndAlertDate"></label>
                                                <input type="date" name="api[useEndAlertDate]" id="api_useEndAlertDate"
                                                        value="{{ old('api.useEndAlertDate', '' == $userDetailList['contractPlan']['api']['useEndAlertDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndAlertDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useEndDate"></label>
                                                <input type="date" name="api[useEndDate]" id="api_useEndDate"
                                                        value="{{ old('api.useEndDate', '' == $userDetailList['contractPlan']['api']['useEndDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndDate']), 'Y-m-d'))}}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                    <div class="flex flex-col">
                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                    <table id="apiTable2" class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-green-500">
                                                            <tr>
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
                                                            </tr>
                                                        </thead>

                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <input type="hidden" name="api[ids]" id="api_ids" value="{{ old('api.ids', $userDetailList['contractPlan']['api']['ids']) }}">
                                                                    {{ $userDetailList['contractPlan']['api']['ids'] }}
                                                                </td>
                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_idUnitPrice"></label>
                                                                    <input type="text" maxlength="10" name="api[idUnitPrice]" id="api_idUnitPrice" value="{{ old('api.idUnitPrice', $userDetailList['contractPlan']['api']['idUnitPrice']) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_searchUnitPrice"></label>
                                                                    <input type="text" maxlength="3" name="api[searchUnitPrice]" id="api_searchUnitPrice" value="{{ old('api.searchUnitPrice', $userDetailList['contractPlan']['api']['searchUnitPrice']) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_searchCount"></label>
                                                                    <input type="text" name="api[searchCount]" id="api_searchCount" value="{{ old('api.searchCount', $userDetailList['contractPlan']['api']['searchCount']) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_deposit"></label>
                                                                    <input type="text" maxlength="10" name="api[deposit]" id="api_deposit" value="{{ old('api.deposit', $userDetailList['contractPlan']['api']['deposit']) }}"
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
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-0">
                                    <div class="text-right">
                                        <button type="button" id="btnApiAdd"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                追加
                                        </button>
                                    </div>
                                </div>
                                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                    <div class="flex flex-col">
                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                    <input type="hidden" name="apiNum" id="apiNum"
                                                           value="{{ old('apiNum', count($userDetailList['contractPlan']['api']['userDetail'])) }}">

                                                    <table id="apiTable3" class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-green-500">
                                                            <tr>
                                                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                                    No
                                                                </th>
                                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                                    ステータス
                                                                </th>
                                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                                    ユーザーID
                                                                </th>
                                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
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
                                                            </tr>
                                                        </thead>

                                                        <tbody class="bg-white divide-y divide-gray-200">

                                                        @php
                                                            $num = 0;
                                                        @endphp

                                                        @foreach( $userDetailList['contractPlan']['api']['userDetail'] as $item)
                                                            @php
                                                                /* @var  $num */
                                                                /* @var  $userDetailList */
                                                                /* @var  $loop */
                                                                $num =   $loop->index + 1;
                                                            @endphp

                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $num }}
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <select name="api[userDetail][{{ $num }}][delFlg]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                            <option value="0" {{ $item['delFlg'] == 0 ? 'selected' : '' }}>有効</option>
                                                                            <option value="1" {{ $item['delFlg'] == 1 ? 'selected' : '' }}>無効</option>
                                                                        </select>
                                                                    </label>
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <input type="hidden" name="api[userDetail][{{ $num }}][userId]" id="api_userId_{{ $num }}" value="{{ $item['userId'] }}">
                                                                    {{$item['userId']}}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                    <input type="hidden" name="api[userDetail][{{ $num }}][password]" id="api_password_{{ $num }}" value="{{ $item['password'] }}">
                                                                    {{$item['password']}}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="api[userDetail][{{ $num }}][name]" id="api_name_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.name', $num), $item['name']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="api[userDetail][{{ $num }}][departmentJob]" id="api_departmentJob_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.departmentJob', $num), $item['departmentJob']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="api[userDetail][{{ $num }}][mail]" id="api_mail_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.mail', $num), $item['mail']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        @for ($i = 0; $i < old('apiNum', 0) - $num; $i++)
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <select name="addApiDelFlg[]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                            <option value="0" {{ old('addApiDelFlg.' . $i) == 0 ? 'selected' : '' }}>有効</option>
                                                                            <option value="1" {{ old('addApiDelFlg.' . $i) == 1 ? 'selected' : '' }}>無効</option>
                                                                        </select>
                                                                    </label>
                                                                </td>
                                                                <td class="user px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <input type="hidden" name="addWebUserId[]" value="{{ old('addApiUserId.' . $i) }}">
                                                                    {{ old('addApiUserId.' . $i) }}

                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="addApiName[]" value="{{ old('addApiName.' . $i) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="addApiDepartmentJob[]" value="{{ old('addApiDepartmentJob.' . $i) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="addApiDepartmentJobMail[]" value="{{ old('addApiDepartmentJobMail.' . $i) }}"
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        @endfor
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
                        @if( $userDetailList['userCompany']['companyId'] === '' )
                            <button type="button" onclick="location.href = '{{ route('manageUser') }}';"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            一覧に戻る
                            </button>
                        @else
                            <button type="button" onclick="location.href = '{{ route('manageUserDetail', ['editId' => $userDetailList['userCompany']['companyId']]) }}';"
                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                詳細に戻る
                            </button>
                        @endif
                        <div class="w-2"></div>

                        <button type="submit"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            更新
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <table id="addWebLine" class="hidden">
        <tbody>
            <tr>
                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <select name="addWebDelFlg[]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="0">有効</option>
                            <option value="1">無効</option>
                        </select>
                    </label>
                </td>
                <td class="user px-3 py-4 whitespace-nowrap text-sm font-medium border">
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <input type="text" maxlength="20" name="addWebName[]"
                               class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </label>
                </td>
                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <input type="text" maxlength="20" name="addWebDepartmentJob[]"
                               class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </label>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <input type="text" name="addWebDepartmentJobMail[]"
                               class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </label>
                </td>
            </tr>
        </tbody>
    </table>

    <table id="addApiLine" class="hidden">
        <tbody>
        <tr>
            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
            </td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                <label>
                    <select name="addApiDelFlg[]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="0">有効</option>
                        <option value="1">無効</option>
                    </select>
                </label>
            </td>
            <td class="user px-3 py-4 whitespace-nowrap text-sm font-medium border">
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                <label>
                    <input type="text" maxlength="20" name="addApiName[]"
                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                </label>
            </td>
            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                <label>
                    <input type="text" maxlength="20" name="addApiDepartmentJob[]"
                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                </label>
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                <label>
                    <input type="text" name="addApiDepartmentJobMail[]"
                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                </label>
            </td>
        </tr>
        </tbody>
    </table>


    <script>
        let planList = @json($selectList['contractPlan']);

        $(function() {

            $('#web_contractPlanId').change(function() {
                let selectId = $(this).val();

                $.each(planList, function(key, value) {
                    if (value.contractPlanId === selectId) {
                        $('#web_idUnitPrice').val(value.idPrice);
                        $('#web_searchUnitPrice').val(value.unitPrice);

                    }
                });

            });

            $('#api_contractPlanId').change(function() {
                let selectId = $(this).val();

                $.each(planList, function(key, value) {
                    if (value.contractPlanId === selectId) {
                        $('#api_idUnitPrice').val(value.idPrice);
                        $('#api_searchUnitPrice').val(value.unitPrice);

                    }
                });

            });


            $('#userCompany_companyId').on('blur', function() {
                let companyId = $('#userCompany_companyId').val();

                $('#webTable3 tbody td.user').each( function( index, element ) {
                    let tmp = "000" + String( index + 1 );
                    let formatNum = tmp.substr(tmp.length - 3);

                    let userName = 'jcis-' + companyId + '-' + formatNum;
                    $(element).text(userName);

                    let inputName = '<input type="hidden" name="addWebUserId[]" value="' + userName + '">'
                    $(element).append(inputName);

                });

                $('#apiTable3 tbody td.user').each( function( index, element ) {
                    let tmp = "000" + String( index + 1 );
                    let formatNum = tmp.substr(tmp.length - 3);

                    let userName = 'jcisapi-' + companyId + '-' + formatNum;
                    $(element).text(userName);

                    let inputName = '<input type="hidden" name="addApiUserId[]" value="' + userName + '">'
                    $(element).append(inputName);

                });


            });



            $('#btnWebAdd').on('click', function() {

                let companyId = $('#userCompany_companyId').val();
                if (companyId === '') {
                    alert('会社IDを入力してください。')
                    return;
                }

                let webNum = $('#webNum');
                let num = parseInt(webNum.val()) + 1;
                webNum.val(num);

                $('#addWebLine tbody tr:first').clone(true).appendTo("#webTable3 tbody");

                let tmp = "000" + String( num );
                let formatNum = tmp.substr(tmp.length - 3);

                let target = $('#webTable3 tbody tr:last td.user');

                let userName = 'jcis-' + companyId + '-' + formatNum;
                target.append(userName);

                let inputName = '<input type="hidden" name="addWebUserId[]" value="' + userName + '">'
                target.append(inputName);

            });

            $('#btnApiAdd').on('click', function() {

                let companyId = $('#userCompany_companyId').val();
                if (companyId === '') {
                    alert('会社IDを入力してください。')
                    return;
                }

                let apiNum = $('#apiNum');
                let num = parseInt(apiNum.val()) + 1;
                apiNum.val(num);

                $('#addApiLine tbody tr:first').clone(true).appendTo("#apiTable3 tbody");

                let tmp = "000" + String( num );
                let formatNum = tmp.substr(tmp.length - 3);

                let target = $('#apiTable3 tbody tr:last td.user');

                let userName = 'jcisapi-' + companyId + '-' + formatNum;
                target.append(userName);

                let inputName = '<input type="hidden" name="addApiUserId[]" value="' + userName + '">'
                target.append(inputName);

            });


        });
    </script>

@endsection
