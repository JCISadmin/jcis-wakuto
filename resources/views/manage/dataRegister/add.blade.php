@extends('manage.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                データ一括登録画面
            </h1>
        </div>
    </header>

    <main>

        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @csrf
            <div class="flex flex-col">
                <div class="py-3 px-3 border">
                    <div class="py-3">
                        <form method="post" action="{{ route('manageDataRegisterUpload') }}" enctype="multipart/form-data" class="form-horizontal">
                            @csrf
                            <label class="px-10 font-medium">一括データ登録</label>
                            <input type="file" name="csv_file" class="w-1/2 px-6 py-2 justify-center border rounded-md shadow-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                            <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                UPLOAD
                            </button>
                        </form>
                    </div>
                </div>

                <div>
                    @foreach ($errorInfo as $item)
                        {{ $item['row'] }}
                        {{ $item['no'] }}
                        {{ $item['errId'] }}
                        {{ $item['errMsg'] }}
                    @endforeach

                </div>



            </div>
        </div>

    </main>


@endsection
