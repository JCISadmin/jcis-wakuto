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

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageDataEditSearch') }}">
                @csrf
                <div class="flex flex-col">
                    <div class="flex flex-row">
                        <div class="flex-initial px-2 py-2 w-40">対象</div>
                        <div>
                            <label class="px-2">
                                <input type="radio" name="typeId" value="1" {{ $typeId == 1 ? 'checked="checked"' : '' }}>
                                法人
                            </label>
                            <label class="px-2">
                                <input type="radio" name="typeId" value="2" {{ $typeId == 2 ? 'checked="checked"' : '' }}>
                                個人
                            </label>
                        </div>
                    </div>
                    <div class="flex flex-row">
                        <div class="flex-initial px-2 py-2 w-40"><label for="inputName">会社名/氏名</label></div>
                        <div class="flex-initial w-1/4">
                            <input type="text" maxlength="130"  name="inputName" id="inputName" value="{{ $inputName }}"
                                   class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="flex-initial px-6">
                            <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                検索
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            会社名/氏名
                                        </th>
                                        <th scope="col" class="w-40 px-3 py-3 text-left text-xs font-medium text-white border">
                                            事案年月日
                                        </th>
                                        <th scope="col" class="w-60 px-3 py-3 text-left text-xs font-medium text-white border">
                                            要件区分
                                        </th>
                                        <th scope="col" class="w-20 px-3 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                        <th scope="col" class="w-20 px-3 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($dataList as $item)
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->inputName }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ date_format(new Datetime($item->caseDate), 'Y/m/d') }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->requireDivision}}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                                <button type="button" onclick="location.href = '{{ route($editRouteName, ['editId' => $item->editId]) }}';"
                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    編集
                                                </button>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                                <button type="button" onclick="deleteConfirm({{ $item->editId }});"
                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    削除
                                                </button>
                                                <form id="{{ $item->editId }}" method="post" action="{{ route($deleteRouteName, ['editId' => $item->editId]) }}">
                                                    @csrf
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

        </div>


    </main>

    <script>
        function deleteConfirm(editId) {
            let formId = '#' + editId;

            if(window.confirm('削除してよろしいですか？')) {
                $(formId).submit();
            }
        }

    </script>


@endsection
