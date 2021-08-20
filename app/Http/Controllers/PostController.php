<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index','show','search');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Post::select('posts.*','users.name as author')
            ->join('users','posts.author_id','=','users.id')
            ->orderBy('posts.created_at','desc')
            ->paginate(4);
        return view('posts.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = new Post();
        //автор поста -текущий пользователь
        $post->author_id=Auth::id();
        $post ->title = $request->input('title');
        $post->excerpt=$request->input('excerpt');
        $post ->body=$request->input('body');
        $post->save();
        return redirect()->route('post.index')->with('success','Новый пост успешно создан');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::select('posts.*','users.name as author')
            ->join('users','posts.author_id','=','users.id')
            ->find($id);
        return view('posts.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        //пользователь может редактировать только свои посты
        if (!$this->checkRights($post)) {
            return redirect()
                ->route('post.index')
                ->withFragment('Вы можете редактировать только свои посты');
        }
        return view('posts.edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id)
    {
        //
        $post = Post::findOrFail($id);
        //пользователь может редактировать только свои посты
        if (!$this->checkRights($post)) {
            return redirect()
                ->route('post.index')
                ->withFragment('Вы можете редактировать только свои посты');
        }
        $post->title = $request->input('title');
        $post->excerpt = $request->input('excerpt');
        $post->body = $request->input('body');
        // если надо удалить старое изображение
        if ($request->input('remove')) {
            $this->removeImage($post);
        }
        // если было загружено новое изображение
        $this->uploadImage($request, $post);
        // все готово, можно сохранять пост в БД
        $post->update();
        return redirect()
            ->route('post.show', compact('id'))
            ->with('success', 'Пост успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $post = Post::findOrFail($id);
        //пользователь может редактировать только свои посты
        if (!$this->checkRights($post)) {
            return redirect()
                ->route('post.index')
                ->withFragment('Вы можете редактировать только свои посты');
        }
        $this->removeImage($post);
        $post->delete();
        return redirect()
            ->route('post.index')
            ->with('success','Пост был успешно удален');
    }
    public function search(Request $request){
        $search = $request->input('search','');
        //обрезаем  слишком длинный запрос
        $search = iconv_substr($search,0,64);
        //удаляем все , кроме букв и цифр
        $search = preg_replace('#[^0-9a-zA-ZA-Яа-яёЁ]#u',' ',$search);
        // сжимаем двойные пробелы
        $search = preg_replace('#\s+#u',' ',$search);
        if (empty($search)){
            return view('posts.search');
        }
        $posts = Post::select('posts.*','users.name as author')
            ->join('users','posts.author_id','=','users.id')
            ->where('posts.title','like','%'.$search.'%')//поиск по заголовку поста
            ->orWhere('posts.body','like','%'.$search.'%')//поиск по тексту поста
            ->orWhere('users.name','like','%'.$search.'%')//поиск по автору поста
            ->orderBy('posts.created_at','desc')
            ->paginate(4)
            ->appends(['search'=>$request->input('search')]);
        return view('posts.search',compact('posts'));
    }
    private function uploadImage(Request $request,Post $post){
        //если надо удалить, страое изображение
        if ($request->input('remove')){
            if (!empty($post->image)){
                $name = basename($post->image);
                if (Storage::exists('storage/app/image/image'.$name)){
                    Storage::delete('storage/app/image/image/'.$name);
                }
                $post->image=null;
            }
            if (!empty($post->thumb)){
                $name=basename($post->thumb);
                if (Storage::exists('storage/app/image/thumb/'.$name)){
                    Storage::delete('storage/app/image/thumb'.$name);
                }
                $post ->thumb=null;
            }
            // здесь сложнее, мы не знаем, какое у файла расширение
            if (!empty($name)){
                $images = Storage::files('storage/app/image/source');
                $base = pathinfo($name,PATHINFO_FILENAME);
                foreach ($images as $img) {
                    $temp = pathinfo($img,PATHINFO_FILENAME);
                    if ($temp == $base){
                        Storage::delete($img);
                        break;
                    }
                }
            }
        }
        //если было загруженно новое изображение
        $source = $request->file('image');
        if ($source){
            //перед тем , как заружать новое изображиние ,удаляем загруженное ранее
            $this->removeImage();
            $ext = str_replace('jpeg','jpg',$source->extension());
            //уникальное имя файла , под которым сохраним его в в storage/image/source
            $name = md5(uniqid());
            Storage::putFileAs('storage/app/public/image/source',$source,$name.'.'.$ext);
            //создаем jpg изображение для с страницы поста размеровм 1200х400, качество 100%
            $image = Image::make($source)
                ->resizeCanvas(1200,400,'center',false,'dddddd')
                ->encode('jpg',100);
            //сохраняем это изображиние под именем $name.jpg в директории public/image/image
            Storage::put('storage/app/public/image/image/'.$name.'.jpg',$image);
            $image->destroy();
            $post->image=Storage::url('storage/app/public/image/image'.$name.'.jpg');
            //создаем jpg изображение для спска  постов блога  размеровм 600х200, качество 100%
            $thumb = Image::make($source)
                ->resizeCanvas(600,200,'center',false,'dddddd')
                ->encode('jpg',100);
            //сохраняем это  изоброжание под именем $name.jpg в директории public/image/thumb
            Storage::put('storage/app/public/image/thumb'.$name.'.jpg',$thumb);
            $thumb->destroy();
            $post->thumb = Storage::url('storage/app/public/image/thumb/'.$name.'.jpg');
        }
    }
    private function removeImage(Post $post) {
        if (!empty($post->image)) {
            $name = basename($post->image);
            if (Storage::exists('storage/app/public/image/image/' . $name)) {
                Storage::delete('storage/app/public/image/image/' . $name);
            }
            $post->image = null;
        }
        if (!empty($post->thumb)) {
            $name = basename($post->thumb);
            if (Storage::exists('storage/app/public/image/thumb/' . $name)) {
                Storage::delete('storage/app/public/image/thumb/' . $name);
            }
            $post->thumb = null;
        }
        // здесь сложнее, мы не знаем, какое у файла расширение
        if (!empty($name)) {
            $images = Storage::files('storage/app/public/image/source');
            $base = pathinfo($name, PATHINFO_FILENAME);
            foreach ($images as $img) {
                $temp = pathinfo($img, PATHINFO_FILENAME);
                if ($temp == $base) {
                    Storage::delete($img);
                    break;
                }
            }
        }
    }
    private function checkRights(Post $post) {
        return Auth::id() == $post->author_id || Auth::id() == 1;
    }
}
