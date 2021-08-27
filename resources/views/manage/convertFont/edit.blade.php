@extends('manage.layout')

@section('contents')

@php
    if(is_null($editItem)){
        $updateFlg = "false";
    }else{
        $updateFlg = "true";
    }
@endphp

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                旧字体登録変更画面
            </h1>
        </div>
    </header>

    <main> 

        @include('msg')

        <form method="post" action="{{ route('manageConvertFontUpdate') }}">
            @csrf            
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex justify-center">

                    <input type="hidden" name="updateFlg" value="{{ $updateFlg }}">

                    <div class="flex">
                        <div>
                            <div class="flex-initial px-4">
                                <label for="updateTargetCharacter">対象文字</label>
                                @if(is_null($editItem))
                                    <input type="text" value="{{ old('updateTargetCharacter')}}" name="updateTargetCharacter" id="updateTargetCharacter"
                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                @else
                                    <input type="text" value="{{$editItem['editTargetCharacter']}}" name="updateTargetCharacter" id="updateTargetCharacter" disabled
                                            class="px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    <input type="hidden" value="{{$editItem['editTargetCharacter']}}" name="updateTargetCharacter">
                                @endif    
                            </div>
                            <br>
                            <div class="flex-initial px-4">
                                <label for="updateConvertCharacter" class="align-top">変換字体</label>
                                @if(is_null($editItem))
                                    <textarea value="{{ old('updateConvertCharacter')}}" name="updateConvertCharacter" id="updateConvertCharacter" rows="5"
                                            class="vertical-align:top border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </textarea>
                                @else
                                    @php
                                        $editConvertCharacter = str_replace(",", "\r\n", $editItem['editConvertCharacter']);
                                    @endphp
                                    <textarea  value="{{ old('updateConvertCharacter')}}" name="updateConvertCharacter" id="updateConvertCharacter" rows="5"
                                            class="vertical-align:top border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                        {{ $editConvertCharacter }}
                                    </textarea>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex justify-center">
                    <div class="flex-initial px-4 py-4">
                        <button type="reset"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            リセット
                        </button>
                        <button type="submit"
                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            更新
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>
    
@endsection
