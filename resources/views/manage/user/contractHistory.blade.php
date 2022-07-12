@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                契約変更履歴
            </h1>
        </div>
    </header>

    <main>

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
                                            契約期間
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            契約プラン
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            契約形態
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            ID代
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            検索代
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            年検索数
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                    </tr>
                                </thead>

                                @foreach ($contractList as $contractItem)
                                    @php
                                        /* @var  $num */
                                        /* @var  $expenseAdjustList */
                                        /* @var  $loop */
                                        $num =   $loop->index + 1;
                                        $count = count($contractList);
                                    @endphp
                                
                                <tbody class="bg-white divide-y divide-gray-200">

                                    <tr>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border">
                                            {{ $num }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $contractItem->webPlanContractStartDate }}
                                            @if (!is_null($contractItem->webPlanContractStartDate))
                                            ~
                                            @endif
                                            {{ $contractItem->webPlanContractEndDate }}
                                            @if (!is_null($contractItem->webPlanName) && !is_null($contractItem->apiPlanName))
                                                <br>
                                            @endif
                                            {{ $contractItem->apiPlanContractStartDate }}
                                            @if (!is_null($contractItem->apiPlanContractStartDate))
                                            ~
                                            @endif
                                            {{ $contractItem->apiPlanContractEndDate }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $contractItem->webPlanName }}
                                            @if (!is_null($contractItem->webPlanName) && !is_null($contractItem->apiPlanName))
                                                <br>
                                            @endif
                                            {{ $contractItem->apiPlanName }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border">
                                            {{ $contractItem->webTypeName }}
                                            @if (!is_null($contractItem->webPlanName) && !is_null($contractItem->apiPlanName))
                                                <br>
                                            @endif
                                            {{ $contractItem->apiTypeName }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $contractItem->webPlanIdUnitPrice }}
                                            @if (!is_null($contractItem->webPlanName) && !is_null($contractItem->apiPlanName))
                                                <br>
                                            @endif
                                            {{ $contractItem->apiPlanIdUnitPrice }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $contractItem->webPlanSearchUnitPrice }}
                                            @if (!is_null($contractItem->webPlanName) && !is_null($contractItem->apiPlanName))
                                                <br>
                                            @endif
                                            {{ $contractItem->apiPlanSearchUnitPrice }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium border">
                                            {{ $contractItem->webPlanSearchCount }}
                                            @if (!is_null($contractItem->webPlanName) && !is_null($contractItem->apiPlanName))
                                                <br>
                                            @endif
                                            {{ $contractItem->apiPlanSearchCount }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                            <button type="button" onclick="location.href = '{{ route('manageUserDetail', ['editId' => $editId, 'seqNo' => $contractItem->seqNo])  }}';"
                                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                詳細
                                            </button>
                                            @if($num === $count)
                                            <button type="button" onclick="location.href = '{{ route('manageUserContractDelete', ['editId' => $editId])  }}';"
                                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400">
                                                削除
                                            </button>
                                            @endif
                                        </td>
                                    </tr>

                                </tbody>
                                @endforeach

                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="w-1/2">
                </div>

                <div class="w-1/2 text-right">
                    <div class="inline-flex">
                        <button type="button" onclick="location.href = '{{ route('manageUserDetail', ['editId' => $editId]) }}';"
                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                詳細に戻る
                        </button>
                    </div>
                </div>
            </div>

        </div>


    </main>
@endsection
