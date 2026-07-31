@if ($errors->any())
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="py-1 mt-2 text-sm bg-red-200 text-red-700 rounded-md">
            @foreach ($errors->all() as $error)
                <p class="px-4 py-1">{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif

@if (isset($err) && $err !== '')
    <div class="alert alert-danger">
        <p class="alert-danger">{{ $err }}</p>
    </div>
@endif

@if (isset($errAry) && $errAry !== '')
    <div class="alert alert-danger">
        <p class="alert-danger" style="background-position: left top;">
            @foreach ($errAry as $error)
                {{ $error }}<br />
            @endforeach
        </p>
    </div>
@endif

@if (isset($warn) && $warn !== '')
    <div class="alert alert-warning" style="background-color:#fef263;">
        <p class="alert-warning" style="background-color:#fef263; color:black;">{{ $warn }}</p>
    </div>
@endif

@if (isset($msg) && $msg !== '')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-4 mt-2 text-sm bg-green-200 text-green-700 rounded-md">
            <p>{{ $msg }}</p>
        </div>
    </div>
@endif
