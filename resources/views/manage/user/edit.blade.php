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
        <form id="listForm" method="post" action="{{ route('manageUserUpdate') }}">
            @csrf
            <input type="hidden" name="editId" value="{{ $editId }}">
            <input type="hidden" name="seqNo" value="{{ $seqNo }}">
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                                会社名フリガナ
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
                                                        class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    @foreach($selectList['contractStatus'] as $item)
                                                        <option value="{{ $item->contractStatus }}" {{ $item->contractStatus == old('userCompany.contractStatus', $userDetailList['userCompany']['contractStatus']) ? 'selected' : '' }}>{{ $item->name }}</option>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_kana"></label>
                                                <input type="text" maxlength="80" name="userCompany[kana]" id="userCompany_kana" value="{{ old('userCompany.kana', $userDetailList['userCompany']['kana']) }}"
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
                                                <input type="text" maxlength="100" name="userCompany[staffDepartmentJob]" id="userCompany_staffDepartmentJob" value="{{ old('userCompany.staffDepartmentJob', $userDetailList['userCompany']['staffDepartmentJob']) }}"
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                                請求先BCC
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
                                                <input type="text" maxlength="100" name="userCompany[claimDepartmentJob]" id="userCompany_claimDepartmentJob" value="{{ old('userCompany.claimDepartmentJob', $userDetailList['userCompany']['claimDepartmentJob']) }}"
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_claimMailBcc"></label>
                                                <input type="text" name="userCompany[claimMailBcc]" id="userCompany_claimMailBcc" value="{{ old('userCompany.claimMailBcc', $userDetailList['userCompany']['claimMailBcc']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                                支払期限
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                                送付期限
                                            </th>    
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_paymentTerm"></label>
                                                <select name="userCompany[paymentTerm]" id="userCompany_paymentTerm" 
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    <option value="" {{ '' == $userDetailList['userCompany']['paymentTerm'] ? 'selected' : '' }}></option>
                                                    @foreach($selectList['paymentTerm'] as $idx => $item)
                                                        <option value="{{ $idx }}" {{ $idx == old('userCompany.paymentTerm', $userDetailList['userCompany']['paymentTerm']) ? 'selected' : '' }}>{{ $item['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="userCompany_deliveryDate"></label>
                                                <input type="text" name="userCompany[deliveryDate]" id="userCompany_deliveryDate" value="{{ old('userCompany.deliveryDate', $userDetailList['userCompany']['deliveryDate']) }}"
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
                    システム契約
                </h1>
            </div>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-hidden sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="webTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                契約プラン
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                契約形態
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                トライアル開始日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用開始日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用更新日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用終了通知日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用終了予定日
                                            </th>
                                        </tr>
                                    </thead>

                                    @php
                                        /* @var $webDisabled */

                                        $webDisabled = '';

                                        if(old('web.contractPlanId', $userDetailList['contractPlan']['web']['contractPlanId']) == ''){
                                            $webDisabled = 'disabled';
                                        }

                                    @endphp

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_contractPlanId"></label>
                                                <select name="web[contractPlanId]" id="web_contractPlanId"
                                                            class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    @foreach($selectList['contractPlan']['web'] as $item)
                                                        <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId === old('web.contractPlanId', $userDetailList['contractPlan']['web']['contractPlanId']) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_contractTypeId"></label>
                                                <select name="web[contractTypeId]" id="web_contractTypeId" {{ $webDisabled }}
                                                            class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}" {{ $item->contractTypeId === old('web.contractTypeId', $userDetailList['contractPlan']['web']['contractDetail']['contractTypeId']) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_startTrial"></label>
                                                <input type="date" name="web[startTrial]" id="web_startTrial" {{ $webDisabled }}
                                                        value="{{ old('web.startTrial', '' == $userDetailList['contractPlan']['web']['startTrial'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['startTrial']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useStartDate"></label>
                                                <input type="date" name="web[useStartDate]" id="web_useStartDate" {{ $webDisabled }}
                                                        value="{{ old('web.useStartDate', '' == $userDetailList['contractPlan']['web']['useStartDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useStartDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useUpdateDate"></label>
                                                <input type="date" name="web[useUpdateDate]" id="web_useUpdateDate" {{ $webDisabled }}
                                                        value="{{ old('web.useUpdateDate', '' == $userDetailList['contractPlan']['web']['useUpdateDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useUpdateDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useEndAlertDate"></label>
                                                <input type="date" name="web[useEndAlertDate]" id="web_useEndAlertDate" {{ $webDisabled }}
                                                        value="{{ old('web.useEndAlertDate', '' == $userDetailList['contractPlan']['web']['useEndAlertDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndAlertDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="web_useEndDate"></label>
                                                <input type="date" name="web[useEndDate]" id="web_useEndDate" {{ $webDisabled }}
                                                        value="{{ old('web.useEndDate', '' == $userDetailList['contractPlan']['web']['useEndDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
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
                                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                                                    トライアル検索単価
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
                                                                    <input type="text" maxlength="10" name="web[idUnitPrice]" id="web_idUnitPrice" value="{{ old('web.idUnitPrice', $userDetailList['contractPlan']['web']['contractDetail']['idUnitPrice']) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_searchUnitPrice"></label>
                                                                    <input type="text" maxlength="4" name="web[searchUnitPrice]" id="web_searchUnitPrice" value="{{ old('web.searchUnitPrice', $userDetailList['contractPlan']['web']['contractDetail']['searchUnitPrice']) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_searchCount"></label>
                                                                    <input type="text" name="web[searchCount]" id="web_searchCount" value="{{ old('web.searchCount', $userDetailList['contractPlan']['web']['contractDetail']['searchCount']) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_deposit"></label>
                                                                    <input type="text" maxlength="10" name="web[deposit]" id="web_deposit" value="{{ old('web.deposit', $userDetailList['contractPlan']['web']['deposit']) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500  disabled:opacity-50 webTarget">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="web_trialSearchUnitPrice"></label>
                                                                    <input type="text" maxlength="4" name="web[trialSearchUnitPrice]" id="web_trialSearchUnitPrice" value="{{ old('web.trialSearchUnitPrice', $userDetailList['contractPlan']['web']['trialSearchUnitPrice']) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
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
                                        <button type="button" id="btnWebAdd" {{ $webDisabled }}
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 disabled:opacity-50 webTarget">
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
                                                                <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                                    ID通知先BCC
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
                                                                        <select name="web[userDetail][{{ $num }}][delFlg]" class="border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
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
                                                                        <input type="text" maxlength="100" name="web[userDetail][{{ $num }}][departmentJob]" id="web_departmentJob_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.departmentJob', $num), $item['departmentJob']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="web[userDetail][{{ $num }}][mail]" id="web_mail_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.mail', $num), $item['mail']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="web[userDetail][{{ $num }}][idMailBcc]" id="web_idMailBcc_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.idMailBcc', $num), $item['idMailBcc']) }}"
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
                                                                        <select name="addWebDelFlg[]" {{ $webDisabled }} class="border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
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
                                                                        <input type="text" maxlength="20" name="addWebName[]" value="{{ old('addWebName.' . $i) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                                                    </label>
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="100" name="addWebDepartmentJob[]" value="{{ old('addWebDepartmentJob.' . $i) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="addWebDepartmentJobMail[]" value="{{ old('addWebDepartmentJobMail.' . $i) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="addWebDepartmentJobidMailBcc[]" value="{{ old('addWebDepartmentJobidMailBcc.' . $i) }}" {{ $webDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
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
                    <div class="-my-2 overflow-x-hidden sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="apiTable1" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                契約プラン
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                契約形態
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                トライアル開始日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用開始日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用更新日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用終了通知日
                                            </th>
                                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                                利用終了予定日
                                            </th>
                                        </tr>
                                    </thead>

                                    @php
                                        /* @var $apiDisabled */

                                        $apiDisabled = '';

                                        if(old('api.contractPlanId', $userDetailList['contractPlan']['api']['contractPlanId']) == ''){
                                            $apiDisabled = 'disabled';
                                        }

                                    @endphp

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_contractPlanId"></label>
                                                <select name="api[contractPlanId]" id="api_contractPlanId"
                                                            class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                    @foreach($selectList['contractPlan']['api'] as $item)
                                                        <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId === old('api.contractPlanId', $userDetailList['contractPlan']['api']['contractPlanId']) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_contractTypeId"></label>
                                                <select name="api[contractTypeId]" id="api_contractTypeId" {{ $apiDisabled }}
                                                            class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}" {{ $item->contractTypeId == old('api.contractTypeId', $userDetailList['contractPlan']['api']['contractDetail']['contractTypeId']) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_startTrial"></label>
                                                <input type="date" name="api[startTrial]" id="api_startTrial" {{ $apiDisabled }}
                                                        value="{{ old('api.startTrial', '' == $userDetailList['contractPlan']['api']['startTrial'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['startTrial']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useStartDate"></label>
                                                <input type="date" name="api[useStartDate]" id="api_useStartDate" {{ $apiDisabled }}
                                                        value="{{ old('api.useStartDate', '' == $userDetailList['contractPlan']['api']['useStartDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useStartDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useUpdateDate"></label>
                                                <input type="date" name="api[useUpdateDate]" id="api_useUpdateDate" {{ $apiDisabled }}
                                                        value="{{ old('api.useUpdateDate', '' == $userDetailList['contractPlan']['api']['useUpdateDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useUpdateDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useEndAlertDate"></label>
                                                <input type="date" name="api[useEndAlertDate]" id="api_useEndAlertDate" {{ $apiDisabled }}
                                                        value="{{ old('api.useEndAlertDate', '' == $userDetailList['contractPlan']['api']['useEndAlertDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndAlertDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                            </td>
                                            <td class="px-1 py-4 whitespace-nowrap text-sm font-medium border">
                                                <label for="api_useEndDate"></label>
                                                <input type="date" name="api[useEndDate]" id="api_useEndDate" {{ $apiDisabled }}
                                                        value="{{ old('api.useEndDate', '' == $userDetailList['contractPlan']['api']['useEndDate'] ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndDate']), 'Y-m-d'))}}"
                                                        class="px-1 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
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
                                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                                                    トライアル検索単価
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
                                                                    <input type="text" maxlength="10" name="api[idUnitPrice]" id="api_idUnitPrice" value="{{ old('api.idUnitPrice', $userDetailList['contractPlan']['api']['contractDetail']['idUnitPrice']) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_searchUnitPrice"></label>
                                                                    <input type="text" maxlength="4" name="api[searchUnitPrice]" id="api_searchUnitPrice" value="{{ old('api.searchUnitPrice', $userDetailList['contractPlan']['api']['contractDetail']['searchUnitPrice']) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_searchCount"></label>
                                                                    <input type="text" name="api[searchCount]" id="api_searchCount" value="{{ old('api.searchCount', $userDetailList['contractPlan']['api']['contractDetail']['searchCount']) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_deposit"></label>
                                                                    <input type="text" maxlength="10" name="api[deposit]" id="api_deposit" value="{{ old('api.deposit', $userDetailList['contractPlan']['api']['deposit']) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <label for="api_trialSearchUnitPrice"></label>
                                                                    <input type="text" maxlength="4" name="api[trialSearchUnitPrice]" id="api_trialSearchUnitPrice" value="{{ old('api.trialSearchUnitPrice', $userDetailList['contractPlan']['api']['trialSearchUnitPrice']) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
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
                                        <button type="button" id="btnApiAdd" {{ $apiDisabled }}
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 disabled:opacity-50 apiTarget">
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
                                                                <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                                    ID通知先BCC
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
                                                                        <select name="api[userDetail][{{ $num }}][delFlg]" class="border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
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
                                                                        <input type="text" maxlength="100" name="api[userDetail][{{ $num }}][departmentJob]" id="api_departmentJob_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.departmentJob', $num), $item['departmentJob']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="api[userDetail][{{ $num }}][mail]" id="api_mail_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.mail', $num), $item['mail']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="api[userDetail][{{ $num }}][idMailBcc]" id="api_idMailBcc_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.idMailBcc', $num), $item['idMailBcc']) }}"
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
                                                                        <select name="addApiDelFlg[]" {{ $apiDisabled }} class="border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                            <option value="0" {{ old('addApiDelFlg.' . $i) == 0 ? 'selected' : '' }}>有効</option>
                                                                            <option value="1" {{ old('addApiDelFlg.' . $i) == 1 ? 'selected' : '' }}>無効</option>
                                                                        </select>
                                                                    </label>
                                                                </td>
                                                                <td class="user px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <input type="hidden" name="addApiUserId[]" value="{{ old('addApiUserId.' . $i) }}">
                                                                    {{ old('addApiUserId.' . $i) }}

                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="20" name="addApiName[]" value="{{ old('addApiName.' . $i) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                    </label>
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" maxlength="100" name="addApiDepartmentJob[]" value="{{ old('addApiDepartmentJob.' . $i) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="addApiDepartmentJobMail[]" value="{{ old('addApiDepartmentJobMail.' . $i) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                                                                    </label>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    <label>
                                                                        <input type="text" name="addApiDepartmentJobidMailBcc[]" value="{{ old('addApiDepartmentJobidMailBcc.' . $i) }}" {{ $apiDisabled }}
                                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
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
                            <button type="button" onclick="location.href = '{{ route('manageUserDetail', ['editId' => $editId]) }}';"
                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                詳細に戻る
                            </button>
                        @endif
                        <div class="w-2"></div>

                        <button type="submit" onclick="btnAction('update')"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            更新
                        </button>
                    </div>
                </div>
            </div>

            @if ($editId !== '')
            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="w-1/2">
                </div>

                <div class="w-1/2 text-right">
                    <div class="inline-flex">
                        <div class="w-2"></div>

                        <label class="px-6 py-3 justify-center border border-transparent rounded-l-md shadow-sm font-medium text-white bg-green-500">
                            契約更新日
                        </label>
                        <input type="date" name="contractStartDate" id="contractStartDate"value="{{ old('contractStartDate', '')}}"
                                class="px-1 py-3 border border-gray-300 rounded-r-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <div class="w-2"></div>
                                <button type="submit" onclick="btnAction('contractUpdate')"
                                class="px-6 py-3 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            契約更新
                        </button>
                    </div>
                </div>
            </div>
            @endif

        </form>
    </main>

    @php
        /* @var  $addDisabled */
        $addDisabled = '';
    @endphp

    <table id="addWebLine" class="hidden">
        <tbody>
            <tr>
                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <select name="addWebDelFlg[]" {{ $addDisabled }} class="border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
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
                        <input type="text" maxlength="20" name="addWebName[]" {{ $addDisabled }}
                               class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                    </label>
                </td>
                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <input type="text" maxlength="100" name="addWebDepartmentJob[]" {{ $addDisabled }}
                               class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                    </label>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <input type="text" name="addWebDepartmentJobMail[]" {{ $addDisabled }}
                               class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
                    </label>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                    <label>
                        <input type="text" name="addWebDepartmentJobidMailBcc[]" {{ $addDisabled }}
                               class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 webTarget">
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
                    <select name="addApiDelFlg[]" {{ $addDisabled }} class="border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
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
                    <input type="text" maxlength="20" name="addApiName[]" {{ $addDisabled }}
                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                </label>
            </td>
            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                <label>
                    <input type="text" maxlength="100" name="addApiDepartmentJob[]" {{ $addDisabled }}
                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                </label>
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                <label>
                    <input type="text" name="addApiDepartmentJobMail[]" {{ $addDisabled }}
                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                </label>
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                <label>
                    <input type="text" name="addApiDepartmentJobidMailBcc[]" {{ $addDisabled }}
                           class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 disabled:opacity-50 apiTarget">
                </label>
            </td>
        </tr>
        </tbody>
    </table>


    <script>
        let planList = @json($selectList['contractPlan']);
        let trialPlanId = 'trial';
        // let trialPlanList = planlist['web'].filter(function(object){
        //     return object.id == trialPlanId
        // })
        let trialPlanList = planList['web'].find((v) => v.contractPlanId === trialPlanId);

        $(function() {

            $('#web_contractPlanId').change(function() {
                let selectId = $(this).val();
                let nowVal = $("#btnWebAdd").prop("disabled");

                $.each(planList['web'], function(key, value) {
                    if (value.contractPlanId === selectId) {
                        $('#web_idUnitPrice').val(value.idPrice);
                        $('#web_searchUnitPrice').val(value.unitPrice);
                        $('#web_trialSearchUnitPrice').val(trialPlanList.unitPrice);
                    }
                });

                if(selectId !== '' && nowVal === true){
                    $(".webTarget").prop("disabled",false);

                }else if(selectId === '' && nowVal === false){
                    $(".webTarget").prop("disabled",true);
                }
            });

            $('#api_contractPlanId').change(function() {
                let selectId = $(this).val();
                let nowVal = $("#btnApiAdd").prop("disabled");

                $.each(planList['api'], function(key, value) {
                    if (value.contractPlanId === selectId) {
                        $('#api_idUnitPrice').val(value.idPrice);
                        $('#api_searchUnitPrice').val(value.unitPrice);
                        $('#api_trialSearchUnitPrice').val(trialPlanList.unitPrice);
                    }
                });

                if(selectId !== '' && nowVal === true){
                    $(".apiTarget").prop("disabled",false);

                }else if(selectId === '' && nowVal === false){
                    $(".apiTarget").prop("disabled",true);
                }
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

            $('#web_startTrial').change(function() {
                let inputDate = $(this).val();
                let startTrial = new Date(inputDate);
                let useEndDate = startTrial;

                useEndDate.setDate( startTrial.getDate() + 14);
                $('#web_useEndDate').val(formatDate(useEndDate));
                
                let useEndAlertDate = useEndDate;
                useEndAlertDate.setDate( useEndDate.getDate() - 2);
                $('#web_useEndAlertDate').val(formatDate(useEndAlertDate));
            });

            $('#api_startTrial').change(function() {
                let inputDate = $(this).val();
                let startTrial = new Date(inputDate);
                let useEndDate = startTrial;

                useEndDate.setDate( startTrial.getDate() + 14);
                $('#api_useEndDate').val(formatDate(useEndDate));
                
                let useEndAlertDate = useEndDate;
                useEndAlertDate.setDate( useEndDate.getDate() - 2);
                $('#api_useEndAlertDate').val(formatDate(useEndAlertDate));
            });

            $('#web_useStartDate').change(function() {
                let inputDate = $(this).val();
                let useStartDate = new Date(inputDate);
                let useEndDate = useStartDate;

                useEndDate = new Date( useStartDate.getFullYear() + 1, useStartDate.getMonth(), 0);
                $('#web_useEndDate').val(formatDate(useEndDate));
                
                let useEndAlertDate = useEndDate;
                useEndAlertDate = new Date( useEndDate.getFullYear(), useEndDate.getMonth() - 1, 1);
                $('#web_useEndAlertDate').val(formatDate(useEndAlertDate));
            });

            $('#api_useStartDate').change(function() {
                let inputDate = $(this).val();
                let useStartDate = new Date(inputDate);
                let useEndDate = useStartDate;

                useEndDate = new Date( useStartDate.getFullYear() + 1, useStartDate.getMonth(), 0);
                $('#api_useEndDate').val(formatDate(useEndDate));

                let useEndAlertDate = useEndDate;
                useEndAlertDate = new Date( useEndDate.getFullYear(), useEndDate.getMonth() - 1, 1);
                $('#api_useEndAlertDate').val(formatDate(useEndAlertDate));
            });

            $('#web_useUpdateDate').change(function() {
                let inputDate = $(this).val();
                let useUpdateDate = new Date(inputDate);
                let useEndDate = useUpdateDate;

                useEndDate = new Date( useUpdateDate.getFullYear() + 1, useUpdateDate.getMonth(), 0);
                $('#web_useEndDate').val(formatDate(useEndDate));
                
                let useEndAlertDate = useEndDate;
                useEndAlertDate = new Date( useEndDate.getFullYear(), useEndDate.getMonth() - 1, 1);
                $('#web_useEndAlertDate').val(formatDate(useEndAlertDate));
            });

            $('#api_useUpdateDate').change(function() {
                let inputDate = $(this).val();
                let useUpdateDate = new Date(inputDate);
                let useEndDate = useUpdateDate;

                useEndDate = new Date( useUpdateDate.getFullYear() + 1, useUpdateDate.getMonth(), 0);
                $('#api_useEndDate').val(formatDate(useEndDate));

                let useEndAlertDate = useEndDate;
                useEndAlertDate = new Date( useEndDate.getFullYear(), useEndDate.getMonth() - 1, 1);
                $('#api_useEndAlertDate').val(formatDate(useEndAlertDate));
            });

            function formatDate(dt) {
                let y = dt.getFullYear();
                y = y.toString();

                let m = dt.getMonth() + 1;
                m = ('00' + m.toString()).slice(-2);

                let d = dt.getDate();
                d = ('00' + d.toString()).slice(-2);

                return (y + '-' + m + '-' + d);
            }
        });

        function btnAction(type) {
            let action = '{{ route('manageUserUpdate') }}';
            let targetForm = $('#listForm');

            if (type === 'contractUpdate') {
                action = '{{ route('manageUserContractUpdate') }}';
                targetForm.attr('action', action);

            } else {
                targetForm.attr('action', action);
            }

            targetForm.submit();
        }

    </script>

@endsection
