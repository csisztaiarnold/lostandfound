@extends('layout')

@section('main')

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

    <div class="col-xs-12">
        @if($images)
            @foreach($images as $image)
                <img src="{{ URL::to('/item_images') }}/{{ $item->id }}/{{ $image->filename }}_thumb.{{ $image->extension }}" />
            @endforeach
        @endif
    </div>

@endsection