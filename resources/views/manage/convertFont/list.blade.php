@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                旧字体変換マスタ
            </h1>
        </div>
    </header>

    <main>

        @include('msg')

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-0">
            <div class="text-right">
                <button type="button" id="btnAdd" onclick="location.href = '{{ route('manageConvertFontEdit') }}';"
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
                                        <th scope="col" class="w-20 px-3 py-3 text-left text-xs font-medium text-white border">
                                            対象文字
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                            変換字体
                                        </th>
                                        <th scope="col" class="w-20 px-3 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                        <th scope="col" class="w-20 px-3 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($convertFontList as $item)
                                        @php
                                            /* @var  $num */
                                            /* @var  $convertFontList */
                                            /* @var  $loop */
                                            $num = $convertFontList->firstItem() + $loop->index;
                                        @endphp

                                        <tr>
                                            <td class="w-20 px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->targetCharacter }}
                                            </td>

                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                {{ $item->convertCharacter }}
                                            </td>

                                            <td class="w-20 px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                <button type="button" onclick="location.href = '{{ route('manageConvertFontEdit', ['editId' => base64_encode($item->targetCharacter)]) }}';"
                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    編集
                                                </button>

                                            </td>

                                            <td class="w-20 px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                                <button type="button" onclick="deleteConfirm('delForm{{ $num }}');"
                                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                    削除
                                                </button>
                                                <form id="delForm{{ $num }}" method="post" action="{{ route('manageConvertFontDelete', ['editId' => base64_encode($item->targetCharacter)]) }}">
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

            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="w-5/6">
                    {{ $convertFontList->links('paginate') }}
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
