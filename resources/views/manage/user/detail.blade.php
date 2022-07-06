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
                <button type="button" onclick="location.href = '{{ route( 'manageUserChangeHistory', ['editId' => $userDetailList['userCompany']['companyId']]) }}';"
                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        契約変更履歴
                </button>
                <button type="button" onclick="location.href = '{{ route( 'manageUserSearchReport', ['editId' => $userDetailList['userCompany']['companyId']]) }}';"
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
                                            {{ $userDetailList['userCompany']['contractStatusName'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['chargeName'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['chargeMail'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['kana'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['companyId'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['postCode'] }}
                                        </td>
                                        <td class="px-8 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['address'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['tel'] }}
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
                                        {{ $userDetailList['userCompany']['staffName'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        {{ $userDetailList['userCompany']['staffDepartmentJob'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                        {{ $userDetailList['userCompany']['staffTel'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                        {{ $userDetailList['userCompany']['staffMail'] }}
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
                                            {{ $userDetailList['userCompany']['claimName'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['claimDepartmentJob'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['claimTel'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['claimMailTo'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['claimMailCc'] }}
                                        </td>
                                    </tr>
                                </tbody>
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            支払期限
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['userCompany']['paymentTermName'] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(is_null($userDetailList['contractPlan']['web']) === false)
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                システム契約
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
                                            {{ $userDetailList['contractPlan']['web']['contractPlanName'] }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['web']['contractDetail']['contractTypeName'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['startTrial']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['startTrial']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useStartDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useStartDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useUpdateDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useUpdateDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useEndAlertDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndAlertDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['web']['useEndDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['web']['useEndDate']), 'Y/m/d') }}
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
                                                                {{ $userDetailList['contractPlan']['web']['ids'] }}
                                                            </td>
                                                            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['web']['contractDetail']['idUnitPrice'] }}
                                                            </td>
                                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['web']['contractDetail']['searchUnitPrice'] }}
                                                            </td>
                                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['web']['contractDetail']['searchCount'] }}
                                                            </td>
                                                            <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['web']['deposit'] }}
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
                                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                                ログイン情報通知
                                                            </th>
                                                        </tr>
                                                    </thead>

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
                                                                @if($item['delFlg'] === 0)
                                                                    有効
                                                                @elseif($item['delFlg'] === 1)
                                                                    無効
                                                                @endif
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['userId'] }}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                    <span id="{{ sprintf('%s-%s-%s', $userDetailList['userCompany']['companyId'], $userDetailList['contractPlan']['web']['contractPlanId'], $item['userId']) }}">{{ $item['password'] }}</span>
                                                                </td>
                                                                <td class="px-1 py-4 whitespace-nowrap text-center text-sm font-medium border border-l-0">
                                                                    <button type="button" onclick="changePassword('{{ $userDetailList['userCompany']['companyId'] }}', '{{ $userDetailList['contractPlan']['web']['contractPlanId'] }}', '{{ $item['userId'] }}');"
                                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                        変更
                                                                    </button>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['name'] }}
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['departmentJob'] }}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['mail'] }}
                                                                </td>
                                                                <td class="px-2 py-4 text-center whitespace-nowrap text-sm font-medium border">
                                                                    <button type="button" {{ $userDetailList['userCompany']['contractStatus'] == App\Models\BaseModel::STATUS_END ? 'disabled' : '' }}
                                                                            onclick="sendUserInfo('{{ $userDetailList['userCompany']['companyId'] }}', '{{ $userDetailList['contractPlan']['web']['contractPlanId'] }}', '{{ $item['userId'] }}');"
                                                                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 disabled:opacity-50">
                                                                        通知
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    @endforeach
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
        @endif

        @if(is_null($userDetailList['contractPlan']['api']) === false)
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
                                            {{ $userDetailList['contractPlan']['api']['contractPlanName'] }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $userDetailList['contractPlan']['api']['contractDetail']['contractTypeName'] }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['startTrial']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['startTrial']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useStartDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useStartDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useUpdateDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useUpdateDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useEndAlertDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndAlertDate']), 'Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ is_null($userDetailList['contractPlan']['api']['useEndDate']) ? '' : date_format(new Datetime($userDetailList['contractPlan']['api']['useEndDate']), 'Y/m/d') }}
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
                                                                {{ $userDetailList['contractPlan']['api']['ids'] }}
                                                            </td>
                                                            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['api']['contractDetail']['idUnitPrice'] }}
                                                            </td>
                                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['api']['contractDetail']['searchUnitPrice'] }}
                                                            </td>
                                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['api']['contractDetail']['searchCount'] }}
                                                            </td>
                                                            <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                                                {{ $userDetailList['contractPlan']['api']['deposit'] }}
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
                                                            <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-white border">
                                                                ログイン情報通知
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    @foreach( $userDetailList['contractPlan']['api']['userDetail'] as $item)
                                                    @php
                                                        /* @var  $num */
                                                        /* @var  $userDetailList */
                                                        /* @var  $loop */
                                                        $num = $loop->index + 1;
                                                    @endphp
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $num }}
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                @if($item['delFlg'] === 0)
                                                                    有効
                                                                @elseif($item['delFlg'] === 1)
                                                                    無効
                                                                @endif
                                                                </td>
                                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['userId'] }}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border border-r-0">
                                                                    <span id="{{ sprintf('%s-%s-%s', $userDetailList['userCompany']['companyId'], $userDetailList['contractPlan']['api']['contractPlanId'], $item['userId']) }}">{{ $item['password'] }}</span>
                                                                </td>
                                                                <td class="px-1 py-4 whitespace-nowrap text-center text-sm font-medium border border-l-0">
                                                                    <button type="button" onclick="changePassword('{{ $userDetailList['userCompany']['companyId'] }}', '{{ $userDetailList['contractPlan']['api']['contractPlanId'] }}', '{{ $item['userId'] }}');"
                                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                                        変更
                                                                    </button>
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['name'] }}
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['departmentJob'] }}
                                                                </td>
                                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                                    {{ $item['mail'] }}
                                                                </td>
                                                                <td class="px-2 py-4 text-center whitespace-nowrap text-sm font-medium border">
                                                                    <button type="button" {{ $userDetailList['userCompany']['contractStatus'] == App\Models\BaseModel::STATUS_END ? 'disabled' : '' }}
                                                                            onclick="sendUserInfo('{{ $userDetailList['userCompany']['companyId'] }}', '{{ $userDetailList['contractPlan']['api']['contractPlanId'] }}', '{{ $item['userId'] }}');"
                                                                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 disabled:opacity-50">
                                                                        通知
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    @endforeach
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
        @endif
        <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="w-1/2">
            </div>

            <div class="w-1/2 text-right">
                <div class="inline-flex">
                    <button type="button" onclick="location.href = '{{ route('manageUser') }}';"
                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        一覧に戻る
                    </button>

                    <div class="w-2"></div>

                    <button type="submit" onclick="location.href = '{{ route( 'manageUserEdit', ['editId' => $userDetailList['userCompany']['companyId'], 'seqNo' => $userDetailList['contractPlan']['seqNo']] ) }}';"
                            class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        編集
                    </button>
                </div>
            </div>
        </div>
    </main>
    @csrf
    <script>
        function changePassword(companyId, contractPlanId, userId) {

            if(window.confirm('パスワードを変更してよろしいですか？')) {
                let url = '{{ route('manageUserChangePassword') }}';
                let targetId = companyId + '-' + contractPlanId + '-' + userId;

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                });

                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json",
                    data: {
                        companyId: companyId,
                        contractPlanId: contractPlanId,
                        userId: userId,
                    },
                }).done(function (data) {
                    $('#' + targetId).text(data.password);
                    alert('パスワードを変更しました。')
                });
            }
        }

        function sendUserInfo(companyId, contractPlanId, userId) {
            if(window.confirm('ログイン情報を送信してよろしいですか？')) {
                let url = '{{ route('manageUserSendUserInfo') }}';

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                });

                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json",
                    data: {
                        companyId: companyId,
                        contractPlanId: contractPlanId,
                        userId: userId,
                    },
                }).done(function () {
                    alert('ログイン情報を送信しました。')
                });
            }

        }

    </script>

@endsection
