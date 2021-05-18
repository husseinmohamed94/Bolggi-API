@extends('layouts.admin')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex">
        <h6 class="m-0 font-weight-bold text-primary">Create tag</h6>
        <div class="ml-auto">
            <a href="{{route('admin.post-tags.index')}}" class="btn btn-primary"><span class="icon text-white-50">
            <i class="fa fa-home"></i></span>
            <span class="text"> tag</span>
        </a>
        </div>
    </div>
       <div class="crad-body">
        {!! Form::open(['route' => 'admin.post-tags.store','method ' => 'POST' ]) !!}
       <div class="row">
           <div class="col-12">
                <div class="form-group">
                    {!! Form::label('name','Name ') !!}
                    {!! Form::text('name',old('name'),['class' => 'form-control']) !!}
                @error('name') <span> {{$message}}</span> @enderror
                </div>
           </div>
         </div>



         <div class="form-group pt-4">
            {!! Form::submit('submit',['class' => 'btn btn-primary']) !!}

         </div>

        {!! Form::close() !!}

       </div>
</div>



@endsection