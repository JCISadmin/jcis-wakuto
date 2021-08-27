@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                データ登録変更画面
            </h1>
        </div>
    </header>
    <main>

        @include('msg')


        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8  whitespace-nowrap">
            <form method="post" action="{{ route('manageDataEditSearch') }}">
                @csrf
                <div class="flex flex-col">
                    <div class="-my-2 mx-40">
                        <div class="flex-initial">
                                <ul class="py-2 align-middle inline-block min-w-0 sm:px-6 lg:px-8">
                                    <li class="py-2">
                                        <label class="mr-10 w-20  inline-block float-left">対象</label>
                                        <input type="radio" class="" name="typeId" value="corporation" checked="checked"> 法人
                                        <input type="radio" class="ml-8" name="typeId" value="person"> 個人
                                    </li>
                                    <li class="py-2">
                                        <label class="mr-10 w-20 inline-block float-left mt-2">会社名/氏名</label>
                                        <input type="text" value="{{ $inputName ?? '' }}" name="inputName" id="inputName" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </li>
                                    <li class="px-40 py-5 whitespace-nowrap text-sm">
                                        <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            検索
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Replace with your content -->
                <input type="hidden" id="addNum" name="addNum" value="{{ old('addNum', 0) }}">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto m-auto">
                        <div class="py-2 align-middle  inline-block min-w-0 sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-500 whitespace-nowrap">
                                    <tr>
                                        <th scope="col" class="border px-20 py-3 text-left text-xs font-medium text-white">
                                            会社名/氏名
                                        </th>
                                        <th scope="col" class="border px-20 py-3 text-left text-xs font-medium text-white">
                                            事案年月日
                                        </th>
                                        <th scope="col" class="border px-20 py-3 text-left text-xs font-medium text-white">
                                            要件区分
                                        </th>
                                        <th scope="col" class="border px-5 py-3 text-left text-xs font-medium text-white">
                                        </th>
                                        <th scope="col" class="border px-5 py-3 text-left text-xs font-medium text-white">
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($dataList as $item)
                                            @php
                                                /* @var  $dataList */
                                                /* @var  $item */
                                                if(isset($item->corporationId)){
                                                    $editUrl = route('manageDataEditEditCorporation');
                                                    $delUrl = route('manageDataEditDeleteCorporation');
                                                    $editId = $item->corporationId;
                                                }else{
                                                    $editUrl = route('manageDataEditEditPerson');
                                                    $delUrl = route('manageDataEditDeletePerson');
                                                    $editId = $item->personId;
                                                }
                                            @endphp
                                            <tr>
                                                <td class="px-20 py-4 whitespace-nowrap text-sm font-medium border">
                                                    {{ $item->inputName }}
                                                </td>
                                                <td class="px-20 py-4 whitespace-nowrap text-sm font-medium border">
                                                    {{ $item->caseDate }}
                                                </td>
                                                <td class="px-20 py-4 whitespace-nowrap text-sm font-medium border">
                                                    {{ $item->requireDivision}}
                                                </td>
                                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                    <a href="{{ $editUrl . '/' . $editId }}" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">編集</a>
                                                </td>
                                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium border">
                                                <form method="post" action="{{ $delUrl }}">
                                                    @csrf
                                                    <input type="hidden" name="editId" value="{{ $editId }}">
                                                    <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    削除
                                                    </button>
                                                </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex max-w-7xl py-6 sm:px-6 lg:px-8">
                    <div class="w-5/6 mx-auto">
                        {{ $dataList->links('paginate') }}
                    </div>
                </div>

            <!-- /End replace -->
        </div>


    </main>

@endsection