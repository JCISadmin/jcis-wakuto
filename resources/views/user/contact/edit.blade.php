@extends('user.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                お問い合わせ画面
            </h1>
        </div>
    </header>

    <main>

        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <form method="post" action="{{ route('userContactConfirm') }}">
                @csrf

                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="py-3">
                                <label for="subject">件名</label>
                            </div>

                            <div>
                                <select name="subject" id="subject" class="w-1/2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    <option value="">選択してください</option>
                                    @foreach($selectList as $key => $item )
                                        <option value="{{ $key }}" {{ $ssData['subject'] == $key ? 'selected="selected"' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="py-3">
                                <label for="contactDetail">お問い合わせ内容</label>
                            </div>
                            <div>
                                <textarea name="contactDetail" id="contactDetail" wrap="soft"
                                                          class="w-full px-2 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                                          rows="8">{{ $ssData['contactDetail'] }}</textarea>
                            </div>

                            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                <div class="w-1/2">
                                </div>

                                <div class="w-1/2 text-right">
                                    <div class="inline-flex">
                                        <button type="button" onclick="location.href = '{{ route('userHome') }}';"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            戻る
                                        </button>

                                        <div class="w-2"></div>

                                        <button type="submit"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            確認
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
