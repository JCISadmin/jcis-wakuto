@extends('manage.layout')

@section('contents')

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
<!-- Replace with your content -->
    <form method="post" action="{{ route('manageDataEditUpdatePerson') }}">
        @csrf
        <input type="hidden" name="personId" value="{{ $item[0]->personId }}">
        <input type="hidden" id="addNum" name="addNum" value="{{ old('addNum', 0) }}">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto m-auto">
                <div class="py-2 align-middle  inline-block min-w-0 sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table id="userTable" class="min-w-0 divide-y divide-gray-200">
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    氏名(入力用)
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('inputName',$item[0]->inputName) }}" name="inputName" id="inputName" class="w-full px-2 py-2 text-left border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    氏名(表示用)
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                            <input type="text" value="{{ old('dispName',$item[0]->dispName) }}" name="dispName" id="dispName" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    異名・かな(入力用)
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('inputKana',$item[0]->inputKana) }}" name="inputKana" id="inputKana" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    異名・かな(表示用)
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('dispKana',$item[0]->dispKana) }}" name="dispKana" id="dispKana" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    生年月日
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('birthday',$item[0]->birthday) }}" name="birthday" id="birthday" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    当時郵便番号
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('postCode',$item[0]->postCode) }}" name="postCode" id="postCode" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    当時住所
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('address',$item[0]->address) }}" name="address" id="address" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    要件区分
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('requireDivision',$item[0]->requireDivision) }}" name="requireDivision" id="requireDivision" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    当時所属・役職
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('departmentJob',$item[0]->departmentJob) }}" name="departmentJob" id="departmentJob" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    当時所属団体名
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('department',$item[0]->department) }}" name="department" id="department" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    当時所属所在地
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('departmentAddress',$item[0]->departmentAddress) }}" name="departmentAddress" id="departmentAddress" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    事案年月日
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('caseDate',$item[0]->caseDate) }}" name="caseDate" id="caseDate" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    事案概要
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('caseSummary',$item[0]->caseSummary) }}" name="caseSummary" id="caseSummary" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    当時年齢
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('caseAge',$item[0]->caseAge) }}" name="caseAge" id="caseAge" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    処分官署
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('disposalOffice',$item[0]->disposalOffice) }}" name="disposalOffice" id="disposalOffice" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    情報種別
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('infoKind',$item[0]->infoKind) }}" name="infoKind" id="infoKind" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    情報ソース
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('infoSource',$item[0]->infoSource) }}" name="infoSource" id="infoSource" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    ファイル名
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('filename',$item[0]->filename) }}" name="filename" id="filename" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    登録日
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('regDate',$item[0]->regDate) }}" name="regDate" id="regDate" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-green-500 whitespace-nowrap px-20 py-3 whitespace-nowrap text-sm font-medium border">
                                    備考
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium border">
                                    <input type="text" value="{{ old('note',$item[0]->note) }}" name="note" id="note" class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="py-5 px-40">
                    <button type="reset" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">キャンセル</button>
                    <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">更新</button>
                </div>
            </div>
        </div>
    </form>       
</div>





@endsection