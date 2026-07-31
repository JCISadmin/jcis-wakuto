@extends('user.layout')

@section('contents')
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-lg leading-6 font-semibold text-gray-900">
                お知らせ詳細
            </h1>
        </div>
    </header>

    <main>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white py-4 px-6 shadow sm:rounded-lg">
                <h2 class="text-xl font-semibold mb-4">
                    {{ $infomation->summary }}
                </h2>
                <div class="text-sm text-gray-600 mb-2 flex flex-wrap items-center gap-x-4">
                    <span class="inline-flex items-center gap-1">
                        <span class="mdi mdi-calendar-month-outline shrink-0" style="font-size: 1rem;" aria-hidden="true"></span>
                        {{ \Carbon\Carbon::parse($infomation->infoDate)->format('Y年n月j日') }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="mdi mdi-tag-multiple-outline shrink-0" style="font-size: 1rem;" aria-hidden="true"></span>
                        {{ $infomation->infoType == 'info' ? 'info' : 'メンテナンス' }}
                    </span>
                </div>
                <div class="mb-4">
                    {!! nl2br(e($infomation->detail)) !!}
                </div>
                <div class="mt-6">
                    <button type="button" onclick="location.href = '{{ route('userHome') }}';"
                            class="px-6 py-2 border border-gray-300 rounded-md shadow-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                        戻る
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection