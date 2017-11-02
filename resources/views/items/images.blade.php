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

    {!! Form::open(['url' => 'items/images','id' => 'submit-form','files' => true]) !!}

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

@endsection