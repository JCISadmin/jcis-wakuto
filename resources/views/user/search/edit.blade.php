@extends((auth()->user()->type == 1) ? 'manage.layout': 'user.layout')

@section('contents')
    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('userSearchCheckDeposit') }}">
                @csrf

                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-lg leading-6 font-semibold text-gray-900">
                        法人名検索
                    </h1>
                    <div class="py-3"></div>
                    <div class="w-full grid grid-cols-2 gap-2">
                        @for ($i = 0; $i < 10; $i++)
                            <div class="border w-full">
                                <label>
                                    <input type="text" placeholder="法人名" maxlength="20" value="{{ old('companyName.'.$i, '') }}" name="companyName[]"
                                           class="w-full px-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </label>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-lg leading-6 font-semibold text-gray-900">
                        個人名検索
                    </h1>
                    <div class="py-3"></div>
                    <div class="w-full grid grid-cols-2 gap-2">
                        @for ($i = 0; $i < 10; $i++)
                            <div class="border w-full">
                                <label>
                                    <input type="text" placeholder="個人名" maxlength="20" value="{{ old('parsonName.'.$i, '') }}" name="parsonName[]"
                                           class="w-full px-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </label>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex">
                        <div class="flex-initial px-4">
                            <label class="px-2 py-2" for="age">絞り込み(現年齢)</label>
                            <input type="text" maxlength="3" name="age" id="age" value=" {{old('age')}}"
                                   class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <span>※±1歳で検索します</span>
                        </div>
                    </div>

                    <div class="py-3"></div>

                    <div class="flex">
                        <div class="flex-initial px-4">
                            <input type="hidden" id="oldCity" value="{{ old('city', '') }}">
                            <label class="px-2 py-2" for="prefecture">絞り込み(当時住所)</label>
                            <select name="prefecture" id="prefecture"
                                    class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="" {{ '' == old('prefecture') ? 'selected' : '' }}>都道府県</option>
                                @foreach($selectList['prefecture']['prefecture'] as $item)
                                    <option value="{{ $item }}" {{ $item == old('prefecture') ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach

                            </select>
                            <label for="city"></label>
                            <select name="city" id="city"
                                    class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="" {{ '' == old('city') ? 'selected' : '' }}>市区</option>
                            </select>
                        </div>
                    </div>

                    <div class="py-3"></div>

                    <div class="flex">
                        <div class="flex-initial px-4">
                            <label class="px-2 py-2" for="fuzzyFlg">あいまい検索</label>
                            <input type="hidden" name="fuzzyFlg" value='false'>
                            <input type="checkbox" name="fuzzyFlg" id="fuzzyFlg" value='true' {{ old('fuzzyFlg', 'true') == 'true' ? 'checked="checked"' : '' }}>
                            <span>※旧漢字・複雑漢字を検索に含めます。</span>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="w-1/2">
                        </div>

                        <div class="w-1/2 text-right">
                            <div class="inline-flex">
                                <button type="submit"
                                        class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                    検索
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </main>

    <script>
        $(function() {

            let cityList = @json($selectList['prefecture']['city']);

            $('#prefecture').ready(function(){

                let pref = $('option:selected').val();
                
                if (pref != '') {

                    let city = $('#city');
                    var oldCity = $('#oldCity').val();

                    $('#city option').remove();
                    city.append($('<option value="">市区</option>'));

                    cityList[pref].forEach(function(cityName){

                        var optionTag = '<option value="' + cityName + '"';

                        if (cityName == oldCity) {
                            optionTag = optionTag + ' selected ';
                        }
                        city.append($(optionTag + '>' + cityName + '</option>'));
                });
                }
            });

            $('#prefecture').change(function(){
                let pref = $('option:selected').val();

                let city = $('#city');

                $('#city option').remove();
                city.append($('<option value="">市区</option>'));

                cityList[pref].forEach(function(cityName){
                    city.append($('<option value="' + cityName + '">' + cityName + '</option>'));
                });

            });

        });

    </script>

@endsection
