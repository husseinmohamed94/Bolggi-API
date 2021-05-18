@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('backend/vendor/select2/css/select2.min.css')}}">

@endsection
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex">
        <h6 class="m-0 font-weight-bold text-primary">Edit  Post {{$post->title}}</h6>
        <div class="ml-auto">
            <a href="{{route('admin.posts.index')}}" class="btn btn-primary"><span class="icon text-white-50">
            <i class="fa fa-home"></i></span>
            <span class="text"> Posts</span>
        </a>
        </div>
    </div>
       <div class="crad-body">
        {!! Form::model($post,['route' => ['admin.posts.update',$post->id],'method' => 'patch' ,'files' => true]) !!}
       <div class="row">
           <div class="col-12">
                <div class="form-group">
                    {!! Form::label('title','Title') !!}
                    {!! Form::text('title',old('title',$post->title),['class' => 'form-control']) !!}
                @error('title') <span> {{$message}}</span> @enderror
                </div>
           </div>
       </div>

       <div class="row">
           <div class="col-12">
               <div class="form-group">
                    {!! Form::label('description','description') !!}
                    {!! Form::textarea('description',old('description',$post->description),['class' => 'form-control summernote']) !!}
                     @error('description') <span> {{$message}}</span> @enderror
                 </div>
             </div>
        </div>
           <div class="row">
               <div class="col-12">
                   <div class="form-group">
                       {!! Form::label('tags','Tags') !!}
                       <button type="button" class="btn btn-primary btn-xs" id="select_btn_tag">Select All</button>
                       <button type="button" class="btn btn-primary btn-xs" id="deselect_btn_tag">Deselect All</button>

                       {!! Form::select('tags[]',$tags->toArray(),old('tags'),['class' => 'form-control selects','multiple' => 'multiple','id' => 'select_all_tags']) !!}
                       @error('tags') <span> {{$message}}</span> @enderror
                   </div>
               </div>
           </div>
         <div class="row">
            <div class="col-4">
                <div class="form-group">
                    {!! Form::label('category_id','category_id') !!}
                    {!! Form::select('category_id',['' => '---'] + $categories->toArray() , old('category_id',$post->category_id),['class' => 'form-control']) !!}
                 @error('category_id') <span> {{$message}}</span> @enderror
                 </div>

            </div>
            <div class="col-4">
                {!! Form::label('comment_able','comment_able') !!}
                {!! Form::select('comment_able',['1' => 'Yes' , '0' => 'No'] , old('comment_able',$post->comment_able),['class' => 'form-control']) !!}
             @error('comment_able') <span> {{$message}}</span> @enderror
            </div>
            <div class="col-4">
                {!! Form::label('status','status') !!}
                {!! Form::select('status',['1' => 'Active','0' => 'Inactive']  , old('status',$post->status),['class' => 'form-control']) !!}
             @error('status') <span> {{$message}}</span> @enderror
            </div>
         </div>
            <div class="row pt-4">
                <div class="col-12">
                    {!! Form::label('Sliders','images') !!}

                    <div class="file-loding">
                        {!! Form::file('images[]',['id' => 'post-images','class' => 'fille-input-overview' , 'multiple' => 'multiple']) !!}
                        <span class="form-text text-muted"> Image width should be 800px 500px</span>
                        @error('images') <span> {{$message}}</span> @enderror

                    </div>
                </div>
            </div>


         <div class="form-group pt-4">
            {!! Form::submit('update',['class' => 'btn btn-primary']) !!}

         </div>

        {!! Form::close() !!}

       </div>
</div>



@endsection

@section('script')
    <script src="{{ asset('backend/vendor/select2/js/select2.full.js')}}"></script>

    <script>
    $(function(){
        $('.summernote').summernote({
            tabsize: 2,
             height: 200,
             toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
        ]
        });
        $('.selects').select2({
            tags:true,
            minimumResultsForSearch: Infinity,
        });
        $('#select_btn_tag').click(function (){
            $('#select_all_tags > option').prop("selected","selected");
            $('#select_all_tags').trigger('change')

        });
        $('#deselect_btn_tag').click(function (){
            $('#select_all_tags > option').prop("selected","");
            $('#select_all_tags').trigger('change')

        });
        $('#post-images').fileinput({
            theme: "fas",
            maxFileCount:{{ 5 - $post->media->count()}},
            allowedFileTypes:['image'],
            showCancel: true,
            showRemove:false,
            showUpload: false,
            overwriteInitial:false,
            initialPreview: [
                @if($post->media->count() > 0)
                @foreach( $post->media as $media)
                    "{{ asset('assets/post/' . $media->file_name)}}",
                  @endforeach
                @endif
            ],

            initialPreviewAsData: true,
            initialPreviewFileType:'image',
            initialPreviewConfig:[
                @if($post->media->count() > 0)
                    @foreach( $post->media as $media)
                    {caption: "{{ $media->file_name }}", size:{{ $media->file_size }}, width:"120px"  , url: "{{route('admin.posts.media.destroy',[$media->id,'_token'=>csrf_token()])}}",key: "{{$media->id}}"},
                    @endforeach
                @endif
            ],

        });


    });
    </script>
@endsection
