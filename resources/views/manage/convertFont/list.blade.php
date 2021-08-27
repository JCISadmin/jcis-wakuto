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

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-8">
            <div class="text-right">
                <form method="post" action="{{ route('manageConvertFontEdit') }}">
                    @csrf
                    <button type="submit" name="editItem" id="btnAdd" value=""
                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        新規追加
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="ConvertFontTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="pl-3.5 pr-9 py-3 text-left text-xs font-medium text-white border">
                                            対象文字
                                        </th>
                                        <th scope="col" class="pl-4 pr-72 py-3 text-left text-xs font-medium text-white border">
                                            変換字体
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach( $convertFontList as $item)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium border">
                                        {{ $item->targetCharacter }}
                                    </td>
                                    <td class="px-4 py-4 text-left whitespace-nowrap text-sm font-medium border">
                                        {{ $item->convertCharacter }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                        <form method="post" action="{{ route('manageConvertFontEdit') }}">
                                            @csrf
                                            <button type="submit" name="editItem" id="btnEdit"
                                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                編集
                                            </button>
                                            <input type="hidden" name="editItem[editTargetCharacter]" value="{{ $item->targetCharacter }}">
                                            <input type="hidden" name="editItem[editConvertCharacter]" value="{{ $item->convertCharacter }}">
                                        </form>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                        <form method="post" action="{{ route('manageConvertFontDelete') }}">
                                            @csrf
                                            <button type="submit" formmethod="post" name="delTargetCharacter" id="btnDelete" value="{{ $item->targetCharacter }}"
                                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
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

            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="w-5/6">
                    {{ $convertFontList->links('paginate') }}
                </div>

            </div>

        </div>

    </main>
    
@endsection
