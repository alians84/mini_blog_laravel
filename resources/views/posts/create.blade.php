@extends('layouts.site',['title'=>'Создать пост'])

@section('content')
    <h1 class="mt-2 mb-3">Создать пост</h1>
    <form method="post" action="{{ route('post.store') }}" enctype="multipart/form-data">
        @include('parts.form')
    </form>
@endsection
