@extends('layouts.admin')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex">
        <h6 class="m-0 font-weight-bold text-primary">Post {{$post->title}}</h6>
        <div class="ml-auto">
            <a href="{{route('admin.posts.create')}}" class="btn btn-primary"><span class="icon text-white-50">
            <i class="fa fa-home"></i>    
            </span>
             <span class="text"> Posts</span>
        </a>
          
        </div>
    </div>
        <div class="table-responsive">
            <table class="table  tabel-hover" >
               
              
                <tbody>
                        <tr>
                            <td colspan="4"><a href="{{route('admin.posts.show',$post->id)}}">{{$post->title}}</a> </td>
                        </tr>   
                        <tr>
                            <th>comments</th>
                            <td>{{$post->comment_able==1 ? $post->comments->count():'Disallow'}}</td>
                            
                            <th>status</th>
                            <td>{{$post->status()}}</td>
                        </tr>
                        <tr>
                            <th>category</th>
                            <td>{{$post->category->name}}</td>
                            <th>Author</th>
                            <td>{{$post->user->name}}</td>
                        </tr>
                        <tr>
                            <th>create date</th>
                            <td>{{$post->created_at->format('d-m-Y:i a')}}</td>
                            <th></th>
                            <td></td>
                            </tr>

                            <tr>
                            <td colspan="4">
                                <div class="row">
                                @if($post->media->count() > 0)
                                   @foreach( $post->media as $media)
                                    <div class="col-2">
                                        <img src= "{{ asset('assets/post/' . $media->file_name)}}" class="img-fluid" >
                                    </div>
                               
                                   @endforeach
                                @endif
                            </div>  
                            </td>
                        </tr>
                   
                  
                </tbody>
              
            </table>
        </div>
</div>


<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex">
        <h6 class="m-0 font-weight-bold text-primary">comments </h6>
      
    </div>
    <div class="table-responsive">
        <table class="table  tabel-hover" >
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Author</th>
                    <th width="40%">comment</th>
                    <th>status</th>
                    <th>Created at</th>
                    <th class="text-center" style="width:30px;">Action</th>
                </tr>
            </thead>
          
            <tbody>
                @forelse ($post->comments as $comment)
                    <tr>
                        <td> <img src="{{get_gravatar($comment->email,50) }}" class="img-circle" ></td>
                        <td>{{$comment->name}}</td>
                        <td>{!! $comment->comment !!}</td>
                        <td>{{$comment->status()}}</td>
                        <td>{{$comment->created_at->format('d-m-Y:i a')}}</td>
                        <td>
                            <div class="btn-group">
                            <a href="{{route('admin.post_comments.edit',$comment->id)}}" class="btn-btn-primary"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0)"
                            onclick="if(confirm('Are you sure to delete this comment')){document.getElementById('comment -delete-{{$comment->id}}').submit();}else{return fales;} "
                            class="btn-btn-danger"><i class="fa fa-trash"></i></a>
                            <form action="{{route('admin.post_comments.destroy',$comment->id)}}" method="POST" id="comment-delete-{{$comment->id}}">
                                @csrf
                                @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
               
                @empty
                    <td colspan="7" class="text-center"> No comment found</td>
                @endforelse
            </tbody>
           
        </table>
    </div>
</div>

@endsection
