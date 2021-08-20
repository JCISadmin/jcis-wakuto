@if ($errors->any())
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="mt-2 text-sm text-red-700">
            <ul role="list" class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
    <div class="alert alert-success">
        <p class="alert-success">{{ $msg }}</p>
    </div>
@endif
