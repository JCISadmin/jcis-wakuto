@extends('user.layout')

@section('contents')
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                ホーム
            </h1>
        </div>
    </header>
    <main>
        @include('msg')

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="border-solid border border-green-500">
                <div class="grid grid-cols-4 gap-4 py-10">
                    <div class="text-center">
                        <button onclick="location.href = '{{ route('userSearch') }}';"
                            class="justify-center border border-transparent rounded-md shadow-sm font-bold text-black hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-green-500 h-60 w-60 text-center" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span class="text-4xl">即時検索</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <button onclick="location.href = '{{ route('userRegistryBulkSearch') }}';"
                            class="justify-center border border-transparent rounded-md shadow-sm font-bold text-black hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-green-500 h-60 w-60 text-center" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-4xl">登記情報検索</span>
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <button onclick="location.href = '{{ route('userCsvBulkSearch') }}';"
                            class="justify-center border border-transparent rounded-md shadow-sm font-bold text-black hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-green-500 h-60 w-60 text-center" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                            </svg>
                            <span class="text-4xl">一括検索</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <button onclick="location.href = '{{ route('userAcurisSearchNote') }}';"
                            class="justify-center border border-transparent rounded-md shadow-sm font-bold text-black hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-green-500 h-60 w-60 text-center" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-4xl">海外検索</span>
                        </button>

                    </div>
                </div>
            </div>

            <div class="py-2">
                &nbsp;
            </div>

            <div class="sm:mx-auto sm:w-full sm:max-w-3xl">
                <div class="bg-white py-2 px-4 shadow sm:rounded-lg sm:px-10 text-base">
                    {!! nl2br(e(config('note.app.homeNote')))  !!}
                </div>
            </div> 
        </div>

    </main>
@endsection

