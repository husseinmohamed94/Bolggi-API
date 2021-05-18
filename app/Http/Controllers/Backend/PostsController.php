<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;
use Intervention\Image\Facades\Image;
use App\Models\User;
class PostsController extends Controller
{

    public function __construct()
    {
        if(\auth()->check()){
            $this->middleware('auth');
        }else{
            return view('backend.auth.login');
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!\auth()->user()->ability('admin','manage_post,show_posts')) {
             return redirect('admin/index');
                        }

        $keyword = (isset(\request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $categoryId = (isset(\request()->category_id) && \request()->category_id != '') ? \request()->category_id : null;
        $tagId = (isset(\request()->tag_id) && \request()->tag_id != '') ? \request()->tag_id : null;
        $status = (isset(\request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(\request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(\request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(\request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';


        $posts = Post::with(['media','user','category','comments'])->wherePostType('post');

        if($keyword != null){
            $posts= $posts->search($keyword);
        }
        if($categoryId != null){
            $posts= $posts->whereCategoryId($categoryId);
        }
        if($tagId != null){
        $posts= $posts->whereHas('tags',function ($query) use ($tagId){
            $query->where('id',$tagId);
        });
    }
        if($status != null){
            $posts= $posts->whereStatus($status);
        }


       $posts= $posts->orderBy($sort_by,$order_by);
       $posts= $posts->paginate($limit_by);








        $categories = Category::orderBy('id','desc')->pluck('name','id');
        return view('backend.posts.index',compact('posts','categories'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
            if(!\auth()->user()->ability('admin','create_posts')){
                return redirect('admin/index');
            }
        $tags = Tag::pluck('name','id');
        $categories = Category::orderBy('id','desc')->pluck('name','id');
        return view('backend.posts.create',compact('categories','tags'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
              if(!\auth()->user()->ability('admin','create_posts')){
                    return redirect('admin/index');
                }
            $validate = Validator::make($request->all(),[
                'title'                 =>'required',
                'description'           => 'required|min:50',
                'status'                =>  'required',
                'comment_able'          => 'required',
                'category_id'           => 'required',
                'images.*'              => 'nullable|mimes:jpg,jpeg,png,gif|max:20000',
                'tags.*'                => 'required',
            ]);
        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $data['title']              = $request->title;
        $data['description']        = Purify::clean($request->description);
        $data['status']             = $request->status;
        $data['post_type']          = 'post';
        $data['comment_able']       = $request->comment_able;
        $data['category_id']        = $request->category_id;

        $post = auth()->user()->posts()->create($data);

        if($request->images && count($request->images) > 0 ){
            $i = 1;
            foreach($request->images as $file){
                $filename = $post->slug.'-'.time().'-'.$i. '.' .$file->getClientOriginalExtension();
                $file_size = $file->getSize();
                $file_type = $file->getMimeType();
                $path = public_path('assets/post/'.$filename);

                Image::make($file->getRealPath())->resize(800,null,function($constraint){
                    $constraint->aspectRatio();
                })->save($path,100);
                $post->media()->create([
                    'file_name'     => $filename,
                    'file_size'     => $file_size,
                    'file_type'     => $file_type,
                ]);
                $i++;

            }
        }
        if(count($request->tags) > 0){
            $new_tags = [];
            foreach ($request->tags as $tag){
                $tag = Tag::firstOrCreate([
                    'id' => $tag
                ],[
                    'name' => $tag
                ]);
                $new_tags[] = $tag->id;
            }
            $post->tags()->sync($new_tags);
        }


        if($request->status == 1){
            Cache::forget('recent_posts');
            Cache::forget('global_tags');
        }

        return redirect()->route('admin.posts.index')->with([
            'message'  => 'post created Successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
              if(!\auth()->user()->ability('admin','display_posts')){
                    return redirect('admin/index');
                }
        $post = Post::with(['media','category','user','comments'])->whereId($id)->wherePostType('post')->first();
        return view('backend.posts.show',compact('post'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         if(!\auth()->user()->ability('admin','update_posts')){
                return redirect('admin/index');
            }
        $tags = Tag::pluck('name','id');

        $categories = Category::orderBy('id','desc')->pluck('name','id');
        $post = Post::with(['media'])->whereId($id)->wherePostType('post')->first();
        return view('backend.posts.edit',compact('categories','post','tags'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /* if(!\auth()->user()->ability('admin','update_posts')){
                return redirect('admin/index');
            }*/
         $validate = Validator::make($request->all(),[


            'title'                 =>'required',
            'description'           => 'required|min:50',
            'status'                =>  'required',
            'comment_able'          => 'required',
            'category_id'           => 'required',
            'images.*'              => 'nullable|mimes:jpg,jpeg,png,gif|max:20000',
             'tags.*'               => 'required',

        ]);
     if($validate->fails()){
         return redirect()->back()->withErrors($validate)->withInput();
     }

     $post = Post::whereId($id)->wherePostType('post')->first();
     if($post){
        $data['title']              = $request->title;
        $data['slug']              = null;

        $data['description']        = Purify::clean($request->description);
        $data['status']             = $request->status;
        $data['comment_able']       = $request->comment_able;
        $data['category_id']        = $request->category_id;
        $post->update($data);

        if($request->images && count($request->images) > 0 ){
            $i = 1;
            foreach($request->images as $file){
                $filename = $post->slug.'-'.time().'-'.$i. '.' .$file->getClientOriginalExtension();
                $file_size = $file->getSize();
                $file_type = $file->getMimeType();
                $path = public_path('assets/post/'.$filename);

                Image::make($file->getRealPath())->resize(800,null,function($constraint){
                   $constraint->aspectRatio();
                })->save($path,100);
                $post->media()->create([
                   'file_name'     => $filename,
                   'file_size'     => $file_size,
                   'file_type'     => $file_type,
                ]);
                $i++;
                }
            }

         if(count($request->tags) > 0){
             $new_tags = [];
             foreach ($request->tags as $tag){
                 $tag = Tag::firstOrCreate([
                     'id' => $tag
                 ],[
                     'name' => $tag
                 ]);
                 $new_tags[] = $tag->id;
             }
             $post->tags()->sync($new_tags);
         }




         return redirect()->route('admin.posts.index')->with([
                'message'  => 'post update Successfully',
                'alert-type' => 'success',
             ]);
     }
     return redirect()->route('admin.posts.index')->with([
        'message'  => ' something was wrong',
        'alert-type' => 'danger',
     ]);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
             if(!\auth()->user()->ability('admin','delete_posts')){
                    return redirect('admin/index');
                }

        $post = Post::whereId($id)->wherePostType('post')->first();
        if($post){
         if($post->media->count() > 0){
             foreach($post->media as $media){
                if(File::exists('assets/post/' .$media->file_name )){
                    unlink('assets/post/' . $media->file_name);
                    }
                 }
             }
             $post->delete();
             return redirect()->route('admin.posts.index')->with([
                'message'  => 'post deleted Successfully',
                'alert-type' => 'success',
             ]);
         }
         return redirect()->route('admin.posts.index')->with([
            'message'  => ' something was wrong',
            'alert-type' => 'danger',
         ]);
    }

    public function removeImage(Request $request){

              if(!\auth()->user()->ability('admin','delete_posts')){
                    return redirect('admin/index');
                }
        $media = PostMedia::whereId($request->media_id)->first();
        if($media){
            if(File::exists('assets/post/' .$media->file_name )){
                unlink('assets/post/' . $media->file_name);
            }
            $media->delete();
            return true;
        }
        return false;
    }




}
