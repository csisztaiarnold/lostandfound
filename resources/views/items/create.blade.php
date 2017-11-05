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

    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
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

        <script type="text/javascript">
            google.maps.event.addDomListener(window, 'load', function () {
                var places = new google.maps.places.Autocomplete(document.getElementById('location'));
                google.maps.event.addListener(places, 'place_changed', function () {

                    var place = places.getPlace();
                    console.log(place);
                    var lat = place.geometry.location.lat();
                    var lng = place.geometry.location.lng();
                    var iframe = '<iframe src="https://maps.google.com/maps?q=' + lat + ',' + lng + '&z=15&output=embed" width="100%" height="350" frameborder="0" style="border:0"></iframe>';
                    $('#location_lat').val(lat);
                    $('#location_lng').val(lng);
                    $('#google-map-container').empty().html(iframe);
                });
            });
        </script>

        <div class="col-xs-12 label-container">
            {!! Form::label('location', __('Where have you lost or found it?')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::text('location', '', ['class="form-control"', 'required']) !!}
        </div>

        {!! Form::hidden('location_lat', '', ['id="location_lat"']) !!}
        {!! Form::hidden('location_lng', '', ['id="location_lng"']) !!}

        <div id="google-map-container"></div>

        <div class="col-xs-12 label-container">
            {!! Form::label('description', __('Describe the item')) !!} <span class="required">*</span>
            <div class="small">{{ __('Describe the item, the location where it was lost or found, and put here your contact info too.') }}</div>
        </div>
        <div class="col-xs-12">
            {!! Form::textarea('description', '', ['class="form-control"', 'required']) !!}
        </div>

        <div class="col-xs-12 label-container">
            {!! Form::label('email', __('Email')) !!}
            <div class="small">{{ __('Submitting an email address is optional, but in case you want to get an email with a link for editing and deleting an item, it\'s mandatory. Otherwise, the article will be deleted automatically after 30 days.') }}</div>
        </div>
        <div class="col-xs-12">
            {!! Form::email('email', '', ['class="form-control"']) !!}
        </div>

        <div class="col-md-12 submit-button-container text-center">
            {!! Form::submit(__('Save and proceed to image upload'), ['class="form-control btn btn-primary"']) !!}
        </div>

        {!! Form::token() !!}

    {!! Form::close() !!}

@endsection