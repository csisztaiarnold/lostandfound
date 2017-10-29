@extends('layout')

@section('main')

    <h2>{{ __('New item') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(['url' => 'items','id' => 'submit-form']) !!}

        <div class="col-xs-12 label-container">
            {!! Form::label('type', __('Lost or Found?')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::select('type', ['lost' => __('I have lost an item!'), 'found' => __('I have found an item!')], null, ['class="form-control"']) !!}
        </div>

        <div class="col-xs-12 label-container">
            {!! Form::label('title', __('What have you lost or found?')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::text('title', '', ['class="form-control"', 'required']) !!}
        </div>

        <div class="col-xs-12 label-container">
            {!! Form::label('location', __('Where have you lost or found it?')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::text('location', '', ['class="form-control"', 'required']) !!}
        </div>
        <!-- TODO: autocomplete locations from the locations table -->

        <div class="col-xs-12 label-container">
            {!! Form::label('description', __('Describe the item')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::textarea('description', '', ['class="form-control"', 'required']) !!}
        </div>

        <div class="col-xs-12 label-container">
            {!! Form::label('email', __('Email')) !!}
        </div>
        <div class="col-xs-12">
            {!! Form::email('email', '', ['class="form-control"']) !!}
        </div>

        <!-- TODO: image upload

        <div class="col-xs-12">
            {{ __('Upload pictures (up to 5)') }}
        </div>

        -->

        <div class="col-md-12 submit-button-container text-center">
            {!! Form::submit(__('Submit'), ['class="form-control btn btn-primary"']) !!}
        </div>

        {!! Form::token() !!}

    {!! Form::close() !!}

@endsection