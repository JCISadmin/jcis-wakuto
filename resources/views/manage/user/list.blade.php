@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                ユーザー一覧画面
            </h1>
        </div>
    </header>

    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageUserSearch') }}">
                @csrf
                <div class="flex">
                    <div class="flex-initial px-4">
                        <label for="companyName">会社名</label>
                        <input type="text" maxlength="20" value="{{ $companyName }}" name="companyName" id="companyName"
                               class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="flex-initial px-4">
                        <label for="contractStatus">契約状況</label>
                        <select name="contractStatus" id="contractStatus"
                                class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="" {{ '' == $contractStatus ? 'selected' : '' }}></option>
                            @foreach($selectList['contractStatus'] as $item)
                                <option value="{{ $item->contractStatus }}" {{ $item->contractStatus == $contractStatus ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-initial px-4">
                        <label for="contractPlan">契約プラン</label>
                        <select name="contractPlan" id="contractPlan"
                                class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="" {{ '' == $contractPlan ? 'selected' : '' }}></option>
                            @foreach($selectList['contractPlan'] as $item)
                                <option value="{{ $item->contractPlanId }}" {{ $item->contractPlanId === $contractPlan ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-initial px-4">
                        <label for="useEndAlertDate">最終通知日</label>
                        <input type="date" value="{{ $useEndAlertDate }}" name="useEndAlertDate" id="useEndAlertDate"
                               class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="flex-initial px-4">
                        <button type="submit"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            検索
                        </button>
                    </div>

                </div>


            </form>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-0">
            <div class="text-right">
                <button type="button" id="btnAdd" onclick="location.href = '{{ route('manageUserEdit') }}';"
                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                    新規追加
                </button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            No
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            契約状況
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            会社名
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            担当者
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white border">
                                            当社窓口
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            ID数
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            契約プラン
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            終了通知日
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            終了予定日
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border w-32">
                                            メモ
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach( $userList as $item)
                                        @php
                                            /* @var  $num */
                                            /* @var  $userList */
                                            /* @var  $loop */
                                            $num = $userList->firstItem() + $loop->index;
                                        @endphp

                                        <tr>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border">
                                                {{ $num }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->statusName }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->staffName }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->chargeName }}
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border">
                                                {{ $item->webPlanIds }}
                                                @if (isset($item->webPlanName) && isset($item->apiPlanName))
                                                    <br>
                                                @endif
                                                {{ $item->apiPlanIds }}
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->webPlanName }}
                                                @if (isset($item->webPlanName) && isset($item->apiPlanName))
                                                    <br>
                                                @endif
                                                {{ $item->apiPlanName }}
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if (isset($item->webPlanUseEndAlertDate))
                                                    {{ date_format(new Datetime($item->webPlanUseEndAlertDate), 'Y/m/d') }}
                                                @endif
                                                @if (isset($item->webPlanName) && isset($item->apiPlanName))
                                                    <br>
                                                @endif
                                                @if (isset($item->apiPlanUseEndAlertDate))
                                                    {{ date_format(new Datetime($item->apiPlanUseEndAlertDate), 'Y/m/d') }}
                                                @endif
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                                @if (isset($item->webPlanUseEndDate))
                                                    {{ date_format(new Datetime($item->webPlanUseEndDate), 'Y/m/d') }}
                                                @endif
                                                @if (isset($item->webPlanName) && isset($item->apiPlanName))
                                                    <br>
                                                @endif
                                                @if (isset($item->apiPlanUseEndDate))
                                                    {{ date_format(new Datetime($item->apiPlanUseEndDate), 'Y/m/d') }}
                                                @endif
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm text-left font-medium border overflow-hidden max-w-0">
                                                <div title="{!! str_replace( "\r\n", "&#13;&#10;" ,$item->memo ) !!}">
                                                    {{ $item->memo }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                                <button type="button" onclick="location.href = '{{ route('manageUserDetail', ['editId' => $item->companyId]) }}';"
                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    詳細
                                                </button>
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

            </div>

        </div>


    </main>

@endsection
