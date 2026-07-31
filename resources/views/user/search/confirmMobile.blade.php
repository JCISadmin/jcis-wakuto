@extends((auth()->user()->type == 1) ? 'manage.layout': 'user.layout')

@section('contents')
<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-lg leading-6 font-semibold text-gray-900">
            検索結果画面
        </h1>
    </div>
</header>

<main class="mx-2">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @if (isset($keyword['company']))
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                法人名検索
            </h1>
            <div class="py-2"></div>
            <div class="flex">
                <div class="flex-initial px-4">
                        @foreach ($keyword['company'] as $isExist => $items)
                            @foreach ($items as $item)
                                検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし" }}<BR>
                            @endforeach
                        @endforeach
                </div>
            </div>
            <div class="py-3"></div>
            @endif
            @if (isset($keyword['person']))
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                個人名検索
            </h1>
            <div class="py-2"></div>
            <div class="flex">
                <div class="flex-initial px-4">
                        @foreach ($keyword['person'] as $isExist => $items)
                            @foreach ($items as $item)
                                検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし" }}<BR>
                            @endforeach
                        @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @php
                /** @var array $item */
            @endphp
            @foreach ($result as $item)
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if ($item['searchType'] === "company")
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    事案年月日
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['formatCaseDate'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    法人・団体名
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['dispName'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    業種
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['industry'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    要件区分
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border ">
                                                    {{ $item['requireDivision'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-6  text-sm font-medium border">
                                                    事案概要
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['caseSummary'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時郵便番号
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['postCode'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時所在地
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['address'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時代表者名
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['delegate'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    所在地の電話番号
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['tel'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    法人番号
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['corporateCode'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2" class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時実質経営者
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['businessOwner'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['department'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    事案個人名
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['casePersonName'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    処分官署
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['disposalOffice'] }}
                                                </td>
                                            </tr>

                                        @elseif ($item['searchType'] === "person")

                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    事案年月日
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['formatCaseDate'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    氏名
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['dispName'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    異名・かな
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['dispKana'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-6  text-sm font-medium border">
                                                    事案概要
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['caseSummary'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    生年月日
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['formatBirthday'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    現年齢(※1)
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['age'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時年齢
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['caseAge'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時郵便番号
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['postCode'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時住所
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['address'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時所属団体名
                                                </td>
                                                <td class="px-4 py-3 w-72 text-sm font-medium border">
                                                    {{ $item['department'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時所属所在地
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['departmentAddress'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    当時所属・役職
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['departmentJob'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    要件区分
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['requireDivision'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white w-20 px-4 py-3  text-sm font-medium border">
                                                    処分官署
                                                </td>
                                                <td class="px-4 py-3  text-sm font-medium border">
                                                    {{ $item['disposalOffice'] }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-3"></div>
            @endforeach
        </div>

        <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="w-5/6">
                {{ $pageNateModel->links('paginate') }}
            </div>
        </div>

        <div class="flex flex-col place-items-center max-w-full mx-auto py-4 sm:px-6 lg:px-8">
            <button type="button" onclick="location.href = '../search';"
                    class="m-2 px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                続けて検索する
            </button>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        ※1)　正確な生年月日が不明な場合は、±1歳の差異が発生する可能性があります。
        </div>
    </div>
</main>

@endsection
