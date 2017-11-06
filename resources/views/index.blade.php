@extends('layout')

@section('main')

    <h2>{{ __('Home Page') }}</h2>

    <script>
        // TODO: Needs refactoring. Low priority.
        // Tries to locate the visitor and displays the google map with all the lost and found items marked
        window.onload = function() {
            var startPos;
            var geoSuccess = function(position) {
                startPos = position;
                var lat = startPos.coords.latitude;
                var lng = startPos.coords.longitude;

                var locations = [
                    @php
                        $items = \App\Item::with('location')->with('images')->get();
                        $itemCount = count($items);
                    @endphp
                    @foreach($items as $item)
                        @php
                            $mainImage = $item->images()->where('image_order',0)->first();
                            $imageHtml = '';
                            if(isset($mainImage)) {
                                $filename = $mainImage->filename;
                                $extension = $mainImage->extension;
                                $imageHtml = '<img src="'.URL::to('/item_images').'/'.$item->id.'/'.$filename.'.'.$extension.'" width="100" style="float:left; margin-right:10px">';
                            }
                        @endphp
                        ['<div style="width:280px"><a href="{{ URL::to('items/show').'/'.$item->id }}">{!! $imageHtml !!}</a> <strong>{{ ($item->type == 'lost') ? __('😪 Lost!') : __('😊 Found!') }}<br /><br /> <a href="{{ URL::to('items/show').'/'.$item->id }}">{{ $item->title }}</a></strong> <br /> {{ $item->location()->first()->location }}</div> <div style="width:280px; clear:both; border-top:1px solid #ddd; margin-top:10px; padding-top:5px">{{ \str_limit($item->description,150,'...') }} <br /><br /> <a href="{{ URL::to('items/show').'/'.$item->id }}">{{ __('More...') }}</a></div></div>', {{ $item->location()->first()->lat }}, {{ $item->location()->first()->lng }}, {{ $itemCount }}],
                        @php($itemCount--)
                    @endforeach
                ];

                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 13,
                    center: new google.maps.LatLng(lat, lng),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                var infowindow = new google.maps.InfoWindow();

                var marker, i;

                for (i = 0; i < locations.length; i++) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map
                    });

                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infowindow.setContent(locations[i][0]);
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }
            };
            navigator.geolocation.getCurrentPosition(geoSuccess);
        };
    </script>

    <div id="map" style="width: 100%; height: 400px;"></div>

    <h2>Get a notification when someone losts or finds and item in your area</h2>

    {!! Form::open(['url' => 'notifications/save','id' => 'notifications-form']) !!}

        <div class="col-xs-12 label-container">
            {!! Form::label('location', __('Location')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::text('location', '', ['class="form-control" id="location"']) !!}
        </div>

        <div class="col-xs-12 label-container">
            {!! Form::label('category_id', __('Category')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::select('category_id', $categories, null, ['class="form-control"']) !!}
        </div>

        <div class="col-xs-12 label-container">
            {!! Form::label('email', __('Email')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12">
            {!! Form::email('email', '', ['class="form-control"']) !!}
        </div>

        <div class="col-xs-12 label-container">
            {!! Form::label('Distance', __('Distance')) !!} <span class="required">*</span>
        </div>
        <div class="col-xs-12" id="slidercontainer">
            <input type="range" min="15" max="500" value="15" step="5" class="slider" id="distance" name="distance">
        </div>

        <div id="distance-text"></div>

        <div class="col-md-12 submit-button-container text-center">
            {!! Form::submit(__('Save notification'), ['class="form-control btn btn-primary"']) !!}
        </div>

        {!! Form::hidden('location_lat', '', ['id="location_lat"']) !!}
        {!! Form::hidden('location_lng', '', ['id="location_lng"']) !!}

        {!! Form::token() !!}

    {!! Form::close() !!}

    <script>
        google.maps.event.addDomListener(window, 'load', function () {
            var places = new google.maps.places.Autocomplete(document.getElementById('location'));
            google.maps.event.addListener(places, 'place_changed', function () {
                var place = places.getPlace();
                console.log(place);
                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();
                $('#location_lat').val(lat);
                $('#location_lng').val(lng);
            });
        });

        var slider = document.getElementById("distance");
        var output = document.getElementById("distance-text");
        output.innerHTML = slider.value; // Display the default slider value

        // Update the current slider value (each time you drag the slider handle)
        slider.oninput = function() {
            output.innerHTML = this.value;
        }
    </script>
@endsection