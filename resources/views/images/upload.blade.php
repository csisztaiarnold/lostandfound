@extends('layout')

@section('main')

    @if ($errors->any())
        <div class="col-xs-12 alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
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
            @foreach($images as $image)
                <img src="{{ URL::to('/item_images') }}/{{ $item->id }}/{{ $image->filename }}_thumb.{{ $image->extension }}" data-image-id="{{ $image->id }}" />
            @endforeach
        @endif
    </div>
    <script>
        $(document).ready(function(){

            sortable('.sortable',{
                items: 'img' ,
                placeholder: '<div style="border:1px solid #888888; background-color:#f5f5f5; margin:0; padding:0; display:inline-block; width:200px; height:200px;"></div>',
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
@endsection