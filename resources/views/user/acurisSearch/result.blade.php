@extends((auth()->user()->type == 1) ? 'manage.layout': 'user.layout')

@section('contents')
    <main>
        @include('msg')

        <div class="flex max-w-7xl text-left mx-auto pt-8 sm:px-6 lg:px-8">
            <div class="w-3/12">
                <button onclick="location.href = '{{ route('userAcurisSearch') }}';"
                    class="px-4 py-2 justify-left border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 mx-3">
                    戻る
                </button>
            </div>
            <div class="w-7/12">
            </div>
            <img class="w-2/12" src="/acuris_icon.jpg">
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @if (isset($keyword['company']))
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                法人(Corporation)
            </h1>
            <div class="py-2"></div>
            <div class="flex">
                <div class="flex-initial px-4">
                        @foreach ($keyword['company'] as $isExist => $items)
                            @foreach ($items as $item)
                                @if($isExist === 'error')
                                    検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　エラーが発生しました。検索代は発生しません。<BR>
                                @else
                                    検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし" }}<BR>
                                @endif
                            @endforeach
                        @endforeach
                </div>
            </div>
            <div class="py-3"></div>
            @endif
            @if (isset($keyword['person']))
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
            個人(Person)
            </h1>
            <div class="py-2"></div>
            <div class="flex">
                <div class="flex-initial px-4">
                        @foreach ($keyword['person'] as $isExist => $items)
                            @foreach ($items as $item)
                                @if($isExist === 'error')
                                    検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　エラーが発生しました。検索代は発生しません。<BR>
                                @else
                                    検索日時:{{ $searchTime }}　検索ワード: {{ $item }}　該当: {{ ($isExist === "exist") ? "あり" : "なし" }}<BR>
                                @endif
                            @endforeach
                        @endforeach
                </div>
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('userAcurisSearchLookupPdf') }}">
            @csrf
            <div id="chkItem" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-4 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table id="userTable" class="min-w-full divide-y divide-gray-200 table-fixed">
                                    <thead class="bg-green-500">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-white border w-0">
                                                <input id="allSelect" type="checkbox" class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-green-600 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-white border w-40">
                                                Name(氏名)
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-white border w-2/12">
                                                Date of Birth(生年月日)
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-white border w-3/12">
                                                DataSets(データセット)
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-white border w-1/12">
                                                Gender(性別)
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-white border w-2/12">
                                                Nationality(国籍)
                                            </th>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-white border w-1/12">
                                                Score(スコア)
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($result as $key => $item)
                                            <tr>
                                                <input type="hidden" name="searchType[{{$key}}]" value="{{ $item['searchType'] }}">
                                                <input type="hidden" name="name[{{$key}}]" value="{{ $item['name'] }}">
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-medium border overflow-hidden">
                                                    <input type="checkbox" name="resourceId[{{$key}}]" value="{{ $item['resourceId'] }}"
                                                        class="lookupChk px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-green-600 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border overflow-hidden max-w-0"
                                                    title="{!! str_replace( "\r\n", "&#13;&#10;", $item['name']  ) !!}">
                                                    {{ $item['name'] }}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border overflow-hidden max-w-0"
                                                    title="{!! str_replace( "\r\n", "&#13;&#10;", isset($item['datesOfBirth']) ?  $item['datesOfBirth'] : '' ) !!}">
                                                    {{ isset($item['datesOfBirth']) ?  $item['datesOfBirth'] : ''}}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border overflow-hidden max-w-0"
                                                    title="{!! str_replace( "\r\n", "&#13;&#10;", $item['datasets']  ) !!}">
                                                    {{ $item['datasets'] }}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border overflow-hidden max-w-0">
                                                    {{ isset($item['gender']) ?  $item['gender'] : ''}}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium border overflow-hidden max-w-0"
                                                title="{!! str_replace( "\r\n", "&#13;&#10;", $item['countries'] ) !!}">
                                                    {{ $item['countries'] }}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-center font-medium border overflow-hidden max-w-0">
                                                    {{ $item['score'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl text-center mx-auto pt-10 sm:px-6 lg:px-8">
                    <button type="button" onclick="window.open('./print', 'newTab');"
                    class="px-4 py-2 justify-left border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 mx-3">
                        印刷
                    </button>

                    <button type="button" onclick="location.href = './pdf';"
                    class="px-4 py-2 justify-left border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 mx-3">
                        PDFでダウンロード
                    </button>

                    <button type="button" onclick="location.href = './excel';"
                    class="px-4 py-2 justify-left border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 mx-3">
                        EXCELでダウンロード
                    </button>

                    <button type="submit" onclick="return searchConfirm()"
                    class="px-4 py-2 justify-left border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 mx-3">
                        詳細情報を検索する
                    </button>
                </div>

                
                <div class="my-6 px-3 py-6 shadow overflow-hidden border border-gray-200 sm:rounded-lg">
                    {!! nl2br(config('note.acurisSearch.result.note'))  !!}
                </div>
                
            </div>
        </form>

    </main>

<script>

    $(function() {
        // 「全て選択」をチェック
        $('#allSelect').on('click', function() {
            var allSelect = $("#allSelect").prop("checked");
            if(allSelect){
                // すべてチェック
                $("#chkItem")
                .find('input[type="checkbox"]')
                .not('#allSelect')
                .prop('checked', true);
                
            }else{
                // すべてチェック外す
                $("#chkItem")
                .find('input[type="checkbox"]')
                .not('#allSelect')
                .prop('checked', false);
            }
        });

        // 「全て選択」以外をチェック
        $("#chkItem").find('input[type="checkbox"]').not('#allSelect').on('click', function() {
            if ($('#chkItem :checked').not('#allSelect').length == $('#chkItem :input').not('#allSelect').length) {
                // 「全て選択」チェック
                $('#allSelect').prop('checked', true);
            } else {
                // 「全て選択」チェック外す
                $('#allSelect').prop('checked', false);
            }
        });
    });

    function searchConfirm() {

        let totalCount = 0;

        $('.lookupChk').each(function( index, element ){
            if( $(element).prop("checked") == true ){
                totalCount++;
            }
        });

        if ( totalCount > 0 ){
            let unitPrice =  "{{$unitPrice}}";
            let totalPrice = unitPrice * totalCount;
            if (window.confirm(totalCount +'件を検索します。\n' + unitPrice + '円 × ' + totalCount + '件 = ' + totalPrice + '円 が課金されますが、よろしいですか？')) {
                return true;
            } else {
                return false;
            }

        } else {
            window.alert('検索対象にチェックを入れてください。');
            return false;
    }
}
</script>


@endsection

