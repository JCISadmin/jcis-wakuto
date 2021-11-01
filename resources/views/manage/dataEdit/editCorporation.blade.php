@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                法人情報変更画面
            </h1>
        </div>
    </header>

    <main>

        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('manageDataEditUpdateCorporation') }}">
                @csrf

                <input type="hidden" name="corporationId" value="{{ $item['corporationId'] }}">

                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                    <tbody>
                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="inputName"><span class="text-white">法人・団体名(入力用)</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="80" name="inputName" id="inputName" value="{{ old('inputName', $item['inputName']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="dispName"><span class="text-white">法人・団体名(表示用)</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="80" name="dispName" id="dispName" value="{{ old('dispName', $item['dispName']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="industry"><span class="text-white">業種</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="60" name="industry" id="industry" value="{{ old('industry', $item['industry']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="postCode"><span class="text-white">当時郵便番号</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="8" name="postCode" id="postCode" value="{{ old('postCode', $item['postCode']) }}"
                                                       class="w-40 px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="address"><span class="text-white">当時団体所在地</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="500" name="address" id="address" value="{{ old('address', $item['address']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="corporateCode"><span class="text-white">法人番号</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="20" name="corporateCode" id="corporateCode" value="{{ old('corporateCode', $item['corporateCode']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="tel"><span class="text-white">所在地の電話番号</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="20" name="tel" id="tel" value="{{ old('tel', $item['tel']) }}"
                                                       class="w-40 px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="requireDivision"><span class="text-white">要件区分</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="50" name="requireDivision" id="requireDivision" value="{{ old('requireDivision', $item['requireDivision']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="businessOwner"><span class="text-white">当時実質経営者</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="20" name="businessOwner" id="businessOwner" value="{{ old('businessOwner', $item['businessOwner']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="department"><span class="text-white">当時経営者所属</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="50" name="department" id="department" value="{{ old('department', $item['department']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="delegate"><span class="text-white">当時代表者</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="20" name="delegate" id="delegate" value="{{ old('delegate', $item['delegate']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="casePersonName"><span class="text-white">事案個人名</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="20" name="casePersonName" id="casePersonName" value="{{ old('casePersonName', $item['casePersonName']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="caseDate"><span class="text-white">事案年月日</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="date"  name="caseDate" id="caseDate" value="{{ old('caseDate', $item['caseDate']) }}"
                                                       class="w-40 px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="caseSummary"><span class="text-white">事案概要</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <textarea maxlength="1000" name="caseSummary" id="caseSummary" wrap="soft"
                                                          class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                                          rows="3">{{ old('caseSummary', $item['caseSummary']) }}</textarea>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="disposalOffice"><span class="text-white">処分官署</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="50" name="disposalOffice" id="disposalOffice" value="{{ old('disposalOffice', $item['disposalOffice']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="infoKind"><span class="text-white">情報種別</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="50" name="infoKind" id="infoKind" value="{{ old('infoKind', $item['infoKind']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="infoSource"><span class="text-white">情報ソース</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text"  maxlength="100" name="infoSource" id="infoSource" value="{{ old('infoSource', $item['infoSource']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="filename"><span class="text-white">ファイル名</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="text" maxlength="80" name="filename" id="filename" value="{{ old('filename', $item['filename']) }}"
                                                       class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="regDate"><span class="text-white">登録日</span></label>
                                            </td>
                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <input type="date" name="regDate" id="regDate" value="{{ old('regDate', $item['regDate']) }}"
                                                       class="w-40 px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w-1/5 bg-green-500 whitespace-nowrap px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <label for="note"><span class="text-white">備考</span></label>
                                            </td>

                                            <td class="w-4/5 px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                                <textarea name="note" id="note" wrap="soft"
                                                          class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                                          rows="3">{{ old('note', $item['note']) }}</textarea>
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
                                        <button type="button" onclick="location.href = '{{ route('manageDataEdit') }}';"
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
