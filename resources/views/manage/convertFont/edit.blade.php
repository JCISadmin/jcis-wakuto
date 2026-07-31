@extends('manage.layout')

@section('contents')


    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                旧字体登録変更画面
            </h1>
        </div>
    </header>

    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageConvertFontUpdate') }}">
                @csrf
                <input type="hidden" name="editId" value="{{ $editId }}">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                    <tbody>
                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="targetCharacter"><span class="text-white">対象文字</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="2" name="targetCharacter" id="targetCharacter" value="{{ old('targetCharacter', $item['targetCharacter']) }}"
                                                       {{ $editId == '' ? '' : 'readonly' }}
                                                       class="w-20 px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="convertCharacter"><span class="text-white">変換字体</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <textarea name="convertCharacter" id="convertCharacter" wrap="soft"
                                                          class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                                          rows="20">{{ old('convertCharacter', $item['convertCharacter']) }}</textarea>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>

                            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                <div class="w-1/2">
                                </div>

                                <div class="w-1/2 text-right">
                                    <div class="inline-flex">
                                        <button type="button" onclick="location.href = '{{ route('manageConvertFont') }}';"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            キャンセル
                                        </button>

                                        <div class="w-2"></div>

                                        <button type="submit"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            更新
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>


            </form>
        </div>


    </main>

@endsection
