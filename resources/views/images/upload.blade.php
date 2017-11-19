@extends('layout')

@section('main')

    <div class="container">

        @if ($errors->any())
            <div class="col-xs-12 alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (\Session::has('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{!! \Session::get('success') !!}</li>
                </ul>
            </div>
        @endif

        @if (\Session::has('error'))
            <div class="alert alert alert-danger">
                <ul>
                    <li>{!! \Session::get('error') !!}</li>
                </ul>
            </div>
        @endif

        <div class="col-xs-12">
            <div class="preview">
                {{ $item->title }}<br />
                <br />
                {{ $item->description }}
            </div>
        </div>

        <h2>{{ __('Upload images') }}</h2>

        @if($image_limit_reached === false)
        {!! Form::open(['url' => 'images/upload','id' => 'submit-form','files' => true]) !!}

        <div class="col-xs-12 label-container">
            {!! Form::label('location', __('Upload an image:')) !!}
        </div>
        <div class="col-xs-12">
            {!! Form::file('image', '', ['class="form-control"', 'required']) !!}
        </div>

        <div class="col-md-12 submit-button-container text-center">
            {!! Form::submit(__('Upload image'), ['class="form-control btn btn-primary"']) !!}
            <a href="{{ URL::to('items/success') }}" title="{{ __('Finish') }}" class="btn btn-danger finish-link">{{ __('Finish') }}</a>
        </div>

        {!! Form::token() !!}

        {!! Form::close() !!}
        @else
            <div class="col-md-12 submit-button-container text-center">
                {{ __('Sorry, but your image limit has reached! Delete an image to upload another one, or finish the submission.') }}<br />
                <a href="{{ URL::to('items/success') }}" title="{{ __('Finish') }}" class="btn btn-danger finish-link">{{ __('Finish') }}</a>
            </div>
        @endif

        <div class="col-xs-12 sortable">
            @if($images)
                <div class="col-xs-12">{{ __('Drag and drop to reorder your images. The first one will be your main one.') }}</div>
                @foreach($images as $image)
                    <div class="image-container" data-image-id="{{ $image->id }}"><img src="{{ URL::to('/item_images') }}/{{ $item->id }}/{{ $image->filename }}_thumb.{{ $image->extension }}" /><br /><a href="{{ URL::to('images/delete/'.$image->id) }}"><button class="btn btn-danger">{{ __('Delete image') }}</button></a></div>
                @endforeach
            @endif
        </div>

        <script>
        $(document).ready(function(){

            sortable('.image-container',{
                items: '.image-container',
            });

            sortable('.sortable')[0].addEventListener('sortupdate', function(e) {

                var index;
                var images = e.detail.newStartList;
                var imageOrderArray = [];
                for (index = 0; index < images.length; ++index) {
                    imageOrderArray[index] = images[index]['dataset']['imageId'];
                }

                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/') }}/images/reorder",
                    data: {
                        imageOrderArray: imageOrderArray,
                        _token: "{{ csrf_token() }}",
                    }
                });

            });

        });
        </script>

    </div>
@endsection