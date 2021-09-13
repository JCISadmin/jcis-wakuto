@extends('user.layout')

@section('contents')

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                一括検索アップロード
            </h1>
        </div>
    </header>

    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <form method="post" action="{{ route('userBulkSearchUpload') }}"  enctype="multipart/form-data">
                            @csrf
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <div class="py-3">
                                    <label class="px-10 font-medium">一括検索データ</label>
                                    <input type="file" name="bulk_file" class="w-1/2 px-6 py-2 justify-center border rounded-md shadow-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                                </div>
                                <div class="py-3 px-10">
                                    <input type="checkbox" id="aimai" name="aimai" {{ (old("checkbox")) }}>
                                    <label class="px-5 font-medium">あいまい検索</label>
                                </div>
                            </div>
                            
                            <div class="flex max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                                <div class="w-1/2">
                                </div>
                            
                                <div class="w-1/2 text-right">
                                    <div class="inline-flex">
                                        <button type="submit"
                                                class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                            アップロード
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection