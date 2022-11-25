@extends((auth()->user()->type == 1) ? 'manage.layout': 'user.layout')

@section('contents')
    <main>
        @include('msg')

            <form method="post" action="{{ route('userAcurisSearchSearch') }}">
                @csrf

                <div class="max-w-7xl mx-auto pt-8 px-4 sm:px-6 lg:px-8">
                    <div class ="flex">
                        <div class="w-3/12">
                            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                                法人(Corporation)
                            </h1>
                        </div>
                        <div class="w-7/12">
                        </div>
                        <img class="w-2/12" src="/acuris_icon.jpg">
                    </div>

                    <div class="flex">

                        <div class="w-3/5">
                            <div class="py-3"></div>
                            <div class="w-full grid grid-cols-2 gap-x-2 gap-y-4">
                                @for ($i = 0; $i < 10; $i++)
                                <div class="border w-full">
                                    <label>
                                        <input type="text" placeholder="Corporation name" value="{{ old('companyName.'.$i, '') }}" name="companyName[]"
                                        class="checkCompany w-full px-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </label>
                                </div>
                                @endfor
                            </div>
                        </div>
                        <div class="w-2/5">
                            <div class="py-3"></div>
                            <div class="w-full mx-2 px-3 pt-2 pb-6 shadow overflow-hidden border border-gray-200 sm:rounded-lg">
                                <div class="my-2">
                                    <label class="ml-2 text-sm text-gray-900">検索条件（複数選択可能)</label>
                                </div>
                                <div id="chkSearchCond">
                                    <input type="hidden" name="datasets" value="">
                                    <div>
                                        <input id="allSelect" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="allSelect" class="ml-2 text-sm text-gray-900">全て選択</label>

                                        <input id="PEP" name="datasets[]" type="checkbox" value="PEP" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="PEP" class="ml-2 text-sm text-gray-900">PEP</label>
                                    </div>
                                    <div>
                                        <input id="SAN-CURRENT" name="datasets[]" type="checkbox" value="SAN-CURRENT" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="SAN-CURRENT" class="ml-2 text-sm text-gray-900">Sanctions – Current 制裁-現在</label>
                                    </div>
                                    <div>
                                        <input id="SAN-FORMER" name="datasets[]" type="checkbox" value="SAN-FORMER" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="SAN-FORMER" class="ml-2 text-sm text-gray-900">Sanctions – Previous 制裁–前</label>
                                    </div>
                                    <div>
                                        <input id="REL" name="datasets[]" type="checkbox" value="REL" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="REL" class="ml-2 text-sm text-gray-900">Regulatory Enforcement Lists 規制執行</label>
                                    </div>
                                    <div>
                                        <input id="DD" name="datasets[]" type="checkbox" value="DD" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="DD" class="ml-2 text-sm text-gray-900">Disqualified Director (UK Only) 失格取締役（イギリスのみ）</label>
                                    </div>
                                    <div>
                                        <input id="INS" name="datasets[]" type="checkbox" value="INS" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="INS" class="ml-2 text-sm text-gray-900">Insolvent (UK & Ireland) [倒産（イギリス・アイルランド）]</label>
                                    </div>
                                    <div>
                                        <input id="RRE" name="datasets[]" type="checkbox" value="RRE" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="RRE" class="ml-2 text-sm text-gray-900">Reputational Risk Exposure [風評リスク]</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-lg leading-6 font-semibold text-gray-900">
                        個人(Person)
                    </h1>
                    <div class="flex">

                        <div class="w-full">
                            <div class="py-3"></div>
                            <div class="w-full grid grid-cols-3 gap-x-2 gap-y-4">
                                @for ($i = 0; $i < 5; $i++)
                                <div class="border w-full">
                                    <label>
                                        <input type="text" placeholder="Forename" value="{{ old('personName.'.$i. '.forename', '') }}" name="personName[{{$i}}][forename]"
                                        class="checkPerson{{$i}} w-full px-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </label>
                                </div>
                                <div class="border w-full">
                                    <label>
                                        <input type="text" placeholder="Middle name" value="{{ old('personName.'.$i. '.middleName', '') }}" name="personName[{{$i}}][middleName]"
                                        class="checkPerson{{$i}} w-full px-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </label>
                                </div>
                                <div class="border w-full">
                                    <label>
                                        <input type="text" placeholder="Surname" value="{{ old('personName.'.$i. '.surname', '') }}" name="personName[{{$i}}][surname]"
                                        class="checkPerson{{$i}} w-full px-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </label>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>


                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex">
                        <div class="flex-initial px-4">
                            <label class="px-2 py-2" for="nationality">国籍(nationality)</label>
                            <select name="nationality" id="nationality" 
                                class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="" {{ '' == old('nationality') ? 'selected' : '' }}>国籍</option>
                                @foreach($selectList['nationalityList'] as $id => $name)
                                <option value="{{ $id }}" {{ $id == old('nationality') ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex-initial px-4">
                            <label class="px-2 py-2" for="dob">生年月日(Date of Birth)</label>
                            <input type="date" name="dob" id="dob" value="{{old('dob')}}"
                                class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    <div class="my-6 px-3 py-6 shadow overflow-hidden border border-gray-200 sm:rounded-lg">
                        {!! nl2br(config('note.acurisSearch.edit.note'))  !!}
                    </div>

                    <div class="flex">
                        <div class="w-1/2">
                        </div>

                        <div class="w-1/2 text-right">
                            <div class="inline-flex">
                                <button type="submit" onclick="return searchConfirm()"
                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                    検索
                                </button>
                            </div>
                        </div>
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
                $("#chkSearchCond")
                .find('input[type="checkbox"]')
                .not('#allSelect')
                .prop('checked', true);
                
            }else{
                // すべてチェック外す
                $("#chkSearchCond")
                .find('input[type="checkbox"]')
                .not('#allSelect')
                .prop('checked', false);
            }
        });

        // 「全て選択」以外をチェック
        $("#chkSearchCond").find('input[type="checkbox"]').not('#allSelect').on('click', function() {
            if ($('#chkSearchCond :checked').not('#allSelect').length == $('#chkSearchCond :input').not('#allSelect').length) {
                // 「全て選択」チェック
                $('#allSelect').prop('checked', true);
            } else {
                // 「全て選択」チェック外す
                $('#allSelect').prop('checked', false);
            }
        });
    });

    function searchConfirm() {

        let checkCnt = $('#chkSearchCond input:checkbox:checked').length;

        if( checkCnt <= 0 ){
            window.alert('検索条件を指定してください。');
            return false
        }

        let companyCount = 0;
        let personCount = 0;
        let totalCount = 0;

        $('.checkCompany').each(function( index, element ){
            if( $(element).val() != '' ){
                companyCount++;
                totalCount++;
            }
        });

        for(let i=0; i <= 4; i++){
            $('.checkPerson'+i).each(function( index, element ){
                if( $(element).val() != '' ){
                    personCount++;
                    totalCount++;
                    return false;
                }
            });
        }

        if ( totalCount > 0 ){
           
            let unitPrice =  "{{$unitPrice}}";
            let totalPrice = unitPrice * totalCount;
            if (window.confirm('法人名：'+ companyCount +'件、個人名：' + personCount + '件を検索します。\n' + unitPrice + '円 × ' + totalCount + '件 = ' + totalPrice + '円 が課金されますが、よろしいですか？')) {
                return true;
            } else {
                return false;
            }

        } else {
            window.alert('法人名または個人名を入力してください。');
            return false;
        }

    }

</script>


@endsection