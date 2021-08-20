@extends('layouts.site', ['title' => $post->title])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-4 mb-4">
                <div class="card-header">
                    <h1>{{ $post->title }}</h1>
                </div>
                <div class="card-body">
                    <img src="{{ $post->image ?? asset('img/default.jpg') }}" alt="" class="img-fluid">
                    <p class="mt-3 mb-0">{{ $post->body }}</p>
                </div>
                <div class="card-footer">
                    <div class="clearfix">
                        <span class="float-left">
                            Автор: {{ $post->author }}
                            <br>
                            Дата: {{ date_format($post->created_at, 'd.m.Y H:i') }}
                        </span>
                        <span class="float-right">
                        @auth
                                @if (auth()->id() == $post->author_id || auth()->id() == 1)
                                    <a href="{{ route('post.edit', ['id' => $post->post_id]) }}"
                                       class="btn btn-dark mr-2">Редактировать</a>
                                    <!-- Форма для удаления поста -->
                                    <form action="{{ route('post.destroy', ['id' => $post->post_id]) }}"
                                          method="post" onsubmit="return confirm('Удалить этот пост?')"
                                          class="d-inline">
                                @csrf
                                        @method('DELETE')
                                <input type="submit" class="btn btn-danger" value="Удалить">
                            </form>
                                @endif
                            @endauth
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
