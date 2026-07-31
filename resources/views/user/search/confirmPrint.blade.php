<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('hds.title').' - '.config('hds.subject.web.regular') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/jpg"  href="{{ asset('/favicon_logo.jpg') }}">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}}
    </style>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
</head>

<script>

$(function(){
    window.print();
});
</script>

<header class="bg-white shadow-sm">
    <div class="mt-12 max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl leading-6 font-semibold text-gray-900">
            {{ config('hds.title').' - '.config('hds.subject.web.regular') }}
        </h1>
    </div>
</header>

<main>
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
            @endif
            @if (isset($keyword['person']))
            <div class="py-3"></div>
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
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    事案年月日
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['formatCaseDate'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    法人・団体名
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['dispName'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    業種
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    {{ $item['industry'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時郵便番号
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['postCode'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    所在地の電話番号
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['tel'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    法人番号
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    {{ $item['corporateCode'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時所在地
                                                </td>
                                                <td colspan="3" class="px-4 py-3 text-sm font-medium border" style="width: 55%;">
                                                    {{ $item['address'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    要件区分
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border " style="width: 15%;">
                                                    {{ $item['requireDivision'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-6  text-sm font-medium border" style="width: 15%;">
                                                    事案概要
                                                </td>
                                                <td colspan="5" class="px-4 py-3 text-sm font-medium border" style="width: 85%;">
                                                    {{ $item['caseSummary'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時代表者名
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['delegate'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    事案個人名
                                                </td>
                                                <td colspan="3" class="px-4 py-3 text-sm font-medium border" style="width: 50%;">
                                                    {{ $item['casePersonName'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時実質経営者
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['businessOwner'] }}
                                                </td>
                                                <td colspan="2" class="px-4 py-3 text-sm font-medium border" style="width: 35%;">
                                                    {{ $item['department'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    処分官署
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    {{ $item['disposalOffice'] }}
                                                </td>
                                            </tr>
                                        @elseif ($item['searchType'] === "person")

                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    事案年月日
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    {{ $item['formatCaseDate'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    氏名
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['dispName'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    異名・かな
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['dispKana'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    現年齢(※1)
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    {{ $item['age'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    生年月日
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['formatBirthday'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    要件区分
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['requireDivision'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時郵便番号
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    {{ $item['postCode'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時所属・役職
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['departmentJob'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    処分官署
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['disposalOffice'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時住所
                                                </td>
                                                <td colspan="3" class="px-4 py-3 text-sm font-medium border" style="width: 50%;">
                                                    {{ $item['address'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15;">
                                                    当時年齢
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 20%;">
                                                    {{ $item['caseAge'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-6  text-sm font-medium border" style="width: 15%;">
                                                    事案概要
                                                </td>
                                                <td colspan="5" class="px-4 py-3 text-sm font-medium border" style="width: 85%;">
                                                    {{ $item['caseSummary'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時所属団体名
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    {{ $item['department'] }}
                                                </td>
                                                <td class="bg-green-500 text-white px-4 py-3 text-sm font-medium border" style="width: 15%;">
                                                    当時団体所在地
                                                </td>
                                                <td colspan="3" class="px-4 py-3 text-sm font-medium border" style="width: 55%;">
                                                    {{ $item['departmentAddress'] }}
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

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        ※1)　正確な生年月日が不明な場合は、±1歳の差異が発生する可能性があります。
        </div>
    </div>
</main>

<footer>
    <div class="text-sm max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        ・本検索サービスを通じて提供する情報は、独自収集した結果に基づくものであり、絶対的な情報を提供するものではありません。<BR>
        ・本サービスを通じて提供する情報は、同名であっても同一性を保証するものではありません。<BR>
        ・本サービスを通じて提供する情報を元に、自己のデータベースを構築することは禁止します。<BR>
        {{ $companyInfo['name'] }}<BR>
        Japan Credit Information Service Co., Ltd.<BR>
        {{ $companyInfo['address'] }}<BR>
        WEB: https://www.jcis.co.jp
    </div>
</footer>