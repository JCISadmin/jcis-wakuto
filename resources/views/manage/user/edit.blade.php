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
                                            <select name="userCompany[contractStatus]" id="userCompany_contractStatus"
                                                    class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                <option value="" {{ '' == $userDetailList['userCompany']['contractStatusName'] ? 'selected' : '' }}></option>
                                                @foreach($selectList['contractStatus'] as $item)
                                                    <option value="{{ $item->contractStatus }}" {{ $item->contractStatus == $userDetailList['userCompany']['contractStatus'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="20" name="userCompany[chargeName]" id="userCompany_chargeName" value="{{ old('userCompany.chargeName', $userDetailList['userCompany']['chargeName']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" name="userCompany[chargeMail]" id="userCompany_chargeMail" value="{{ old('userCompany.chargeMail', $userDetailList['userCompany']['chargeMail']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="40" name="userCompany[name]" id="userCompany_name" value="{{ old('userCompany.staffName', $userDetailList['userCompany']['name']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="5" name="userCompany[companyId]" id="userCompany_companyId" value="{{ old('userCompany.companyId', $userDetailList['userCompany']['companyId']) }}"
                                                        {{ $editId == '' ? '' : 'readonly' }}
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="7" name="userCompany[postCode]" id="userCompany_postCode" value="{{ old('userCompany.postCode', $userDetailList['userCompany']['postCode']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-8 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="50" name="userCompany[address]" id="userCompany_address" value="{{ old('userCompany.address', $userDetailList['userCompany']['address']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="20" name="userCompany[tel]" id="userCompany_tel" value="{{ old('userCompany.staffName', $userDetailList['userCompany']['tel']) }}"
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
                                                <input type="text" maxlength="20" name="userCompany[staffName]" id="userCompany_staffName" value="{{ old('userCompany.staffName', $userDetailList['userCompany']['staffName']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="20" name="userCompany[staffDepartmentJob]" id="userCompany_staffDepartmentJob" value="{{ old('userCompany.staffDepartmentJob', $userDetailList['userCompany']['staffDepartmentJob']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="20" name="userCompany[staffTel]" id="userCompany_staffTel" value="{{ old('userCompany.staffTel', $userDetailList['userCompany']['staffTel']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
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
                                                <input type="text" maxlength="20" name="userCompany[claimName]" id="userCompany_claimName" value="{{ old('userCompany.claimName', $userDetailList['userCompany']['claimName']) }}"
                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="20" name="userCompany[claimDepartmentJob]" id="userCompany_claimDepartmentJob" value="{{ old('userCompany.claimDepartmentJob', $userDetailList['userCompany']['claimDepartmentJob']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="20" name="userCompany[claimTel]" id="userCompany_claimTel" value="{{ old('userCompany.claimTel', $userDetailList['userCompany']['claimTel']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" name="userCompany[claimMailTo]" id="userCompany_claimMailTo" value="{{ old('userCompany.claimMailTo', $userDetailList['userCompany']['claimMailTo']) }}"
                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
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
                                                <select name="web[contractPlanId]" id="web_contractPlanId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @if( isset($userDetailList['contractPlan']['web']))
                                                    <option value="" {{ '' == $userDetailList['contractPlan']['web']['contractPlanId'] ? 'selected' : '' }}></option>
                                                    @foreach($selectList['contractPlan'] as $item)
                                                        <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId == $userDetailList['contractPlan']['web']['contractPlanId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="" selected></option>
                                                    @foreach($selectList['contractPlan'] as $item)
                                                        <option value="{{ $item->contractPlanId }}">{{ $item->name }}</option>
                                                    @endforeach
                                                @endif
                                                </select>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                <select name="web[contractTypeId]" id="web_contractTypeId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @if( isset($userDetailList['contractPlan']['web']))
                                                    <option value="" {{ '' == $userDetailList['contractPlan']['web']['contractTypeId'] ? 'selected' : '' }}></option>
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}" {{ $item->contractTypeId == $userDetailList['contractPlan']['web']['contractTypeId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="" selected></option>
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}">{{ $item->name }}</option>
                                                    @endforeach
                                                @endIf
                                                </select>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['web']))
                                                    <input type="text" name="web[startTrial]" id="web_startTrial" value="{{ old('web.startTrial', date_format(new Datetime($userDetailList['contractPlan']['web']['startTrial']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else        
                                                    <input type="text" name="web[startTrial]" id="web_startTrial" value="{{ old('web.startTrial', '') }}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['web']))
                                                    <input type="text" name="web[useStartDate]" id="web_useStartDate" value="{{ old('web.useStartDate', date_format(new Datetime($userDetailList['contractPlan']['web']['useStartDate']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                    <input type="text" name="web[useStartDate]" id="web_useStartDate" value="{{ old('web.useStartDate', '') }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['web']))
                                                    <input type="text" name="web[useUpdateDate]" id="web_useUpdateDate" value="{{ old('web.useUpdateDate', date_format(new Datetime($userDetailList['contractPlan']['web']['useUpdateDate']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                    <input type="text" name="web[useUpdateDate]" id="web_useUpdateDate" value="{{ old('web.useUpdateDate', '') }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['web']))
                                                    <input type="text" name="web[useEndAlertDate]" id="web_useEndAlertDate" value="{{ old('web.useEndAlertDate', date_format(new Datetime($userDetailList['contractPlan']['web']['useEndAlertDate']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                    <input type="text" name="web[useEndAlertDate]" id="web_useEndAlertDate" value="{{ old('web.useEndAlertDate', '') }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['web']))
                                                <input type="text" name="web[useEndDate]" id="web_useEndDate" value="{{ old('web.useEndDate', date_format(new Datetime($userDetailList['contractPlan']['web']['useEndDate']), 'Y/m/d')) }}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                <input type="text" name="web[useEndDate]" id="web_useEndDate" value="{{ old('web.useEndDate', '') }}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
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
                                                                @php
                                                                    /* @var  $ids*/
                                                                    $ids = 0;
                                                                @endphp
                                                                @if( isset($userDetailList['contractPlan']['web']) )
                                                                    @foreach( $userDetailList['contractPlan']['web']['userDetail'] as $item )
                                                                        @if( $item['delFlg'] === 0)
                                                                            @php
                                                                                $ids ++;
                                                                            @endphp
                                                                        @endif
                                                                    @endforeach
                                                                @endif


                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <input type="hidden" name="web[ids]" id="web_ids" value="{{ $ids }}">
                                                                    {{ $ids }}
                                                                </td>
                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['web']))
                                                                        <input type="text" maxlength="10" name="web[idUnitPrice]" id="web_idUnitPrice" value="{{ old('web.idUnitPrice', $userDetailList['contractPlan']['web']['idUnitPrice']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                    <input type="text" maxlength="10" name="web[idUnitPrice]" id="web_idUnitPrice" value="{{ old('web.idUnitPrice', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['web']))
                                                                        <input type="text" maxlength="3" name="web[searchUnitPrice]" id="web_searchUnitPrice" value="{{ old('web.searchUnitPrice', $userDetailList['contractPlan']['web']['searchUnitPrice']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                        <input type="text" maxlength="3" name="web[searchUnitPrice]" id="web_searchUnitPrice" value="{{ old('web.searchUnitPrice', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['web']))
                                                                        <input type="text" name="web[searchCount]" id="web_searchCount" value="{{ old('web.searchCount', $userDetailList['contractPlan']['web']['searchCount']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                        <input type="text" name="web[searchCount]" id="web_searchCount" value="{{ old('web.searchCount', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['web']))
                                                                        <input type="text" maxlength="10" name="web[deposit]" id="web_deposit" value="{{ old('web.deposit', $userDetailList['contractPlan']['web']['deposit']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                        <input type="text" maxlength="10" name="web[deposit]" id="web_deposit" value="{{ old('web.deposit', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
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
                                        <button type="button" id="btnAdd" onclick="location.href = '';"
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
                                                        @if( isset($userDetailList['contractPlan']['web']) )
                                                            @foreach( $userDetailList['contractPlan']['web']['userDetail'] as $item)
                                                            @php
                                                                /* @var  $num */
                                                                /* @var  $userDetailList */
                                                                /* @var  $loop */
                                                                $num =   $loop->index + 1;
                                                            @endphp
                                                                <tbody class="bg-white divide-y divide-gray-200">
                                                                    <tr>
                                                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            {{ $num }}
                                                                        </td>
                                                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <select name="web[userDetail][{{ $num }}][delFlg]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                                <option value="0" {{ $item['delFlg'] == 0 ? 'selected' : '' }}>有効</option>
                                                                                <option value="1" {{ $item['delFlg'] == 1 ? 'selected' : '' }}>無効</option>
                                                                            </select>
                                                                        </td>
                                                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="hidden" name="web[userDetail][{{ $num }}][userId]" id="web_userId_{{ $num }}" value="{{ $item['userId'] }}">
                                                                            {{$item['userId']}}
                                                                        </td>
                                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                            <input type="hidden" name="web[userDetail][{{ $num }}][passWord]" id="web_passWord_{{ $num }}" value="{{ $item['passWord'] }}">
                                                                            {{$item['passWord']}}
                                                                        </td>
                                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="text" maxlength="20" name="web[userDetail][{{ $num }}][name]" id="web_name_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.name', $num), $item['name']) }}"
                                                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                        </td>
                                                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="text" maxlength="20" name="web[userDetail][{{ $num }}][departmentJob]" id="web_departmentJob_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.departmentJob', $num), $item['departmentJob']) }}"
                                                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                        </td>
                                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="text" name="web[userDetail][{{ $num }}][mail]" id="web_mail_{{ $num }}" value="{{ old(sprintf('web.userDetail.%d.mail', $num), $item['mail']) }}"
                                                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            @endforeach
                                                        @endif
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
                                                <select name="api[contractPlanId]" id="api_contractPlanId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @if( isset($userDetailList['contractPlan']['api']))
                                                    <option value="" {{ '' == $userDetailList['contractPlan']['api']['contractPlanId'] ? 'selected' : '' }}></option>
                                                    @foreach($selectList['contractPlan'] as $item)
                                                        <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId == $userDetailList['contractPlan']['api']['contractPlanId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="" selected></option>
                                                    @foreach($selectList['contractPlan'] as $item)
                                                        <option value="{{ $item->contractPlanId }}">{{ $item->name }}</option>
                                                    @endforeach
                                                @endif
                                                </select>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                <select name="api[contractTypeId]" id="api_contractTypeId"
                                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @if( isset($userDetailList['contractPlan']['api']))
                                                    <option value="" {{ '' == $userDetailList['contractPlan']['api']['contractTypeId'] ? 'selected' : '' }}></option>
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}" {{ $item->contractTypeId == $userDetailList['contractPlan']['api']['contractTypeId'] ? 'selected' : '' }}>{{ $item->name }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="" selected></option>
                                                    @foreach($selectList['contractType'] as $item)
                                                        <option value="{{ $item->contractTypeId }}">{{ $item->name }}</option>
                                                    @endforeach
                                                @endIf
                                                </select>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['api']))
                                                    <input type="text" name="api[startTrial]" id="api_startTrial" value="{{ old('api.startTrial', date_format(new Datetime($userDetailList['contractPlan']['api']['startTrial']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else        
                                                    <input type="text" name="api[startTrial]" id="api_startTrial" value="{{ old('api.startTrial', '') }}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['api']))
                                                    <input type="text" name="api[useStartDate]" id="api_useStartDate" value="{{ old('api.useStartDate', date_format(new Datetime($userDetailList['contractPlan']['api']['useStartDate']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                    <input type="text" name="api[useStartDate]" id="api_useStartDate" value="{{ old('api.useStartDate', '') }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['api']))
                                                    <input type="text" name="api[useUpdateDate]" id="api_useUpdateDate" value="{{ old('api.useUpdateDate', date_format(new Datetime($userDetailList['contractPlan']['api']['useUpdateDate']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                    <input type="text" name="api[useUpdateDate]" id="api_useUpdateDate" value="{{ old('api.useUpdateDate', '') }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['api']))
                                                    <input type="text" name="api[useEndAlertDate]" id="api_useEndAlertDate" value="{{ old('api.useEndAlertDate', date_format(new Datetime($userDetailList['contractPlan']['api']['useEndAlertDate']), 'Y/m/d')) }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                    <input type="text" name="api[useEndAlertDate]" id="api_useEndAlertDate" value="{{ old('api.useEndAlertDate', '') }}"
                                                            class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if( isset($userDetailList['contractPlan']['api']))
                                                <input type="text" name="api[useEndDate]" id="api_useEndDate" value="{{ old('api.useEndDate', date_format(new Datetime($userDetailList['contractPlan']['api']['useEndDate']), 'Y/m/d')) }}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @else
                                                <input type="text" name="api[useEndDate]" id="api_useEndDate" value="{{ old('api.useEndDate', '') }}"
                                                        class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                @endif
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
                                                                @php
                                                                    /* @var  $ids*/
                                                                    $ids = 0;
                                                                @endphp
                                                                @if( isset($userDetailList['contractPlan']['api']) )
                                                                    @foreach( $userDetailList['contractPlan']['api']['userDetail'] as $item )
                                                                        @if( $item['delFlg'] === 0)
                                                                            @php
                                                                                $ids ++;
                                                                            @endphp
                                                                        @endif
                                                                    @endforeach
                                                                @endif


                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    <input type="hidden" name="api[ids]" id="api_ids" value="{{ $ids }}">
                                                                    {{ $ids }}
                                                                </td>
                                                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['api']))
                                                                        <input type="text" maxlength="10" name="api[idUnitPrice]" id="api_idUnitPrice" value="{{ old('api.idUnitPrice', $userDetailList['contractPlan']['api']['idUnitPrice']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                    <input type="text" maxlength="10" name="api[idUnitPrice]" id="api_idUnitPrice" value="{{ old('api.idUnitPrice', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['api']))
                                                                        <input type="text" maxlength="3" name="api[searchUnitPrice]" id="api_searchUnitPrice" value="{{ old('api.searchUnitPrice', $userDetailList['contractPlan']['api']['searchUnitPrice']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                        <input type="text" maxlength="3" name="api[searchUnitPrice]" id="api_searchUnitPrice" value="{{ old('api.searchUnitPrice', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['api']))
                                                                        <input type="text" name="api[searchCount]" id="api_searchCount" value="{{ old('api.searchCount', $userDetailList['contractPlan']['api']['searchCount']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                        <input type="text" name="api[searchCount]" id="api_searchCount" value="{{ old('api.searchCount', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                    @if( isset($userDetailList['contractPlan']['api']))
                                                                        <input type="text" maxlength="10" name="api[deposit]" id="api_deposit" value="{{ old('api.deposit', $userDetailList['contractPlan']['api']['deposit']) }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @else
                                                                        <input type="text" maxlength="10" name="api[deposit]" id="api_deposit" value="{{ old('api.deposit', '') }}"
                                                                                class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                    @endif
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
                                        <button type="button" id="btnAdd" onclick="location.href = '';"
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
                                                        @if( isset($userDetailList['contractPlan']['api']) )
                                                            @foreach( $userDetailList['contractPlan']['api']['userDetail'] as $item)
                                                            @php
                                                                /* @var  $num */
                                                                /* @var  $userDetailList */
                                                                /* @var  $loop */
                                                                $num =   $loop->index + 1;
                                                            @endphp
                                                                <tbody class="bg-white divide-y divide-gray-200">
                                                                    <tr>
                                                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            {{ $num }}
                                                                        </td>
                                                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <select name="api[userDetail][{{ $num }}][delFlg]" class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                                <option value="0" {{ $item['delFlg'] == 0 ? 'selected' : '' }}>有効</option>
                                                                                <option value="1" {{ $item['delFlg'] == 1 ? 'selected' : '' }}>無効</option>
                                                                            </select>
                                                                        </td>
                                                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="hidden" name="api[userDetail][{{ $num }}][userId]" id="api_userId_{{ $num }}" value="{{ $item['userId'] }}">
                                                                            {{$item['userId']}}
                                                                        </td>
                                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                            <input type="hidden" name="api[userDetail][{{ $num }}][passWord]" id="api_passWord_{{ $num }}" value="{{ $item['passWord'] }}">
                                                                            {{$item['passWord']}}
                                                                        </td>
                                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="text" maxlength="20" name="api[userDetail][{{ $num }}][name]" id="api_name_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.name', $num), $item['name']) }}"
                                                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                        </td>
                                                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="text" maxlength="20" name="api[userDetail][{{ $num }}][departmentJob]" id="api_departmentJob_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.departmentJob', $num), $item['departmentJob']) }}"
                                                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                        </td>
                                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                            <input type="text" name="api[userDetail][{{ $num }}][mail]" id="api_mail_{{ $num }}" value="{{ old(sprintf('api.userDetail.%d.mail', $num), $item['mail']) }}"
                                                                                    class="px-2 py-2 border w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            @endforeach
                                                        @endif
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

@endsection
