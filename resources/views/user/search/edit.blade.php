@extends('user.layout')

@section('contents')
    <main>
        @include('msg')
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="">
            @csrf
                <p>
                    法人名検索
                </p>
                <div class="flex">
                    @for ($i = 0; $i < 10; $i++)
                        <div class="flex-initial px-4">
                            <input type="text" placeholder="法人名" maxlength="20" value="" name="companyName[{{$i}}]" id="companyName_{{$i}}"
                                    class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </div>
                        @if(in_array($i, array(1,3,5,7)) === true)
                        </div><div class="flex">
                        @endif
                    @endfor
                </div>
                <br>
                <p>
                    個人名検索
                </p>
                <div class="flex">
                    @for ($i = 0; $i < 10; $i++)
                        <div class="flex-initial px-4">
                            <input type="text" placeholder="個人名" maxlength="20" value="" name="parsonName[{{$i}}]" id="parsonName_{{$i}}"
                                    class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </div>
                        @if(in_array($i, array(1,3,5,7)) === true)
                        </div><div class="flex">
                        @endif
                    @endfor
                </div>
                <br>
                <div class="flex">
                    <div class="flex-initial px-4">
                        <label class="px-2 py-2" for="userId">絞り込み(現年齢)</label>
                        <input type="text" maxlength="3" name="caseAge" id="caseAge" value=" {{old('caseAge')}}"
                                class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <span>※±1歳で検索します</span>
                    </div>
                </div>
                    <div class="flex">
                        <div class="flex-initial px-4">
                            <label class="px-2 py-2" for="userId">絞り込み(当時住所)</label>
                            <select name="prefecture" id="prefecture"
                                    class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="" {{ '' == old('prefecture') ? 'selected' : '' }}>都道府県</option>
                                @foreach($selectList['prefecture'] as $item)
                                <option value="{{ $item->id }}" {{ $item->prefecture == old('prefecture') ? 'selected' : '' }}>{{ $item->prefecture }}</option>
                                @endforeach
                            </select>
                            <select name="city" id="city"
                                    class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    <option value="" {{ '' == old('city') ? 'selected' : '' }}>市区</option>
                                @foreach($selectList['prefecture'] as $item)
                                <option value="{{ $item->id }}" {{ $item->city == old('city') ? 'selected' : '' }}>{{ $item->city }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                <div class="flex">
                    <div class="flex-initial px-4">
                        <label class="px-2 py-2" for="aimaiFlg">あいまい検索</label>
                        <input type="checkbox" name="aimaiFlg" id="aimaiFlg" value="{{ old('aimaiFlg') }}">
                        <span>※旧漢字・複雑漢字を検索に含めます。</span>
                    </div>
                </div>
                <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div class="w-1/2">
                    </div>

                    <div class="w-1/2 text-right">
                        <div class="inline-flex">
                            <button type="button"
                                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                検索
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>



    </main>
@endsection
