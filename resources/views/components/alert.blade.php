@if (session('success'))
    <div class="container">
        <div class="alert alert-success" style="margin-top:16px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div class="container">
        <div class="alert alert-error" style="margin-top:16px;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="container">
        <div class="alert alert-error" style="margin-top:16px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
