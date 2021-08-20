@extends('layouts.site')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <br>
            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
            </form>

        </div>
    </div>
@endsection
