@extends('layouts.site',['title'=>'Редактировать пост'])

@section('content')
    <h1 class="mt-2 mb-3">Редактировать пост</h1>
    <form method="post" action="{{ route('post.update', ['id' => $post->post_id]) }}"
          enctype="multipart/form-data">
        @method('PATCH')
        @include('parts.form')
    </form>
@endsection
