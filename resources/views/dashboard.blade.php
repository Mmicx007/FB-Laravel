@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mt-5 mb-4">Welcome ! <br> {{ Auth::user()->name }}!</h1>
            <!-- You can replace `Auth::user()->name` with the actual variable containing your username -->
        </div>
    </div>
</div>
@endsection
