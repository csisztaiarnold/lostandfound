@extends('layout')

@section('main')
    <div class="container">
        <div id="current-location-header">
            {{ __('Your current location is') }} <span class="exact-location"></span>
        </div>
        <div id="we-could-determine-your-location">
            {{ __('We couldn\'t determine your exact location. Please use the input field below to locate yourself.') }}
        </div>

        <div class="col-xs-12 location-input-container">

            <input type="text" name="your-current-location" id="your-current-location" class="location-input" placeholder="Change your location" />

            <script>
                google.maps.event.addDomListener(window, 'load', function () {
                    var places = new google.maps.places.Autocomplete(document.getElementById('your-current-location'));
                    google.maps.event.addListener(places, 'place_changed', function () {
                        var place = places.getPlace();
                        var lat = place.geometry.location.lat();
                        var lng = place.geometry.location.lng();
                        loadGoogleMapsWithLocations(lat,lng,'reload');
                        reverseGeolocation(lat,lng);
                        $('#current-location-header').show();
                        $('#we-could-determine-your-location').hide();
                    });
                });
            </script>

        </div>

        <script>

            // TODO: Needs refactoring. Low priority.

            // Reverse geocode for finding current location's name
            function reverseGeolocation(lat, lng)
            {
                var geocoder;
                geocoder = new google.maps.Geocoder();

                var latlng = new google.maps.LatLng(lat, lng);
                geocoder.geocode({'latLng': latlng}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $('.exact-location').text(results[1].formatted_address);
                        }
                    }
                });
            }

            function loadNearbyItems(lat, lng)
            {
                $('#ajax-loader-anim').show();
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/') }}/items/list-items-on-homepage",
                    data: {
                        lat: lat,
                        lng: lng,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(data){
                        var itemData = jQuery.parseJSON(data);
                        var html = '';
                        if(itemData['data'].length === 0) {
                            html = '{{ __('No items nearby...') }}';
                        } else {
                            itemData['data'].forEach(function(entry) {
                                var type = '{{ __('Lost') }}';
                                if(entry['type'] === 'found') {
                                    type = '{{ __('Found') }}';
                                }

                                var image = '';
                                if(entry['filename']) {
                                    image = '<img src="{{ URL::to('item_images') }}/' + entry['item_id'] + '/' + entry['filename'] + '_thumb.' + entry['extension'] + '" alt="' + entry['title'] + '" class="item-image" width="100" />';
                                }
                                html += '<article>';
                                html += '<strong>' + type + '</strong>: <a href="{{ URL::to('items') }}/' + entry['item_id'] + '" title="' + entry['title'] + '">' + entry['title'] + '</a><br />';
                                html += '<div class="location">' + entry['location'] + '</div>';
                                html +=  '<div class="description">' + image + entry['description'] + '</div>';
                                html += '</article>'
                            });
                        }
                        $('#ajax-loader-anim').hide();
                        $('#latest-item-list').hide().html(html).fadeIn('slow');
                    }
                });
            }

            // Tries to locate the visitor and displays the google map with all the lost and found items marked
            window.onload = function() {
                $('#current-location-header').hide();
                @if($location_lat_cookie && $location_lng_cookie)
                    loadGoogleMapsWithLocations({{ $location_lat_cookie }},{{ $location_lng_cookie }},'');
                @else
                var startPos;
                navigator.geolocation.getCurrentPosition(function(position) {
                    startPos = position;
                    var lat = startPos.coords.latitude;
                    var lng = startPos.coords.longitude;
                    loadGoogleMapsWithLocations(lat,lng,'');
                },
                function (error) {
                    // In case geolocation is disabled, try to locate the user by stored cookies
                    if (error.code == error.PERMISSION_DENIED || error.code == error.POSITION_UNAVAILABLE || error.code == error.TIMEOUT || error.code == error.UNKNOWN_ERROR) {
                        @if($location_lat_cookie && $location_lng_cookie)
                            var lat = {{ $location_lat_cookie }};
                            var lng = {{ $location_lng_cookie }};
                        @endif
                        loadGoogleMapsWithLocations(lat,lng,'');
                    }
                });
                @endif
            };

            function loadGoogleMapsWithLocations(lat,lng,reload)
            {
                $('#map').css('width','100%').css('height','400px');
                var locations = [
                        @php
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
                    ['<div style="width:280px"><a href="{{ URL::to('items').'/'.$item->id }}">{!! $imageHtml !!}</a> <strong>{{ ($item->type == 'lost') ? __('😪 Lost!') : __('😊 Found!') }}<br /><br /> <a href="{{ URL::to('items').'/'.$item->id }}">{{ $item->title }}</a></strong> <br /> {{ $item->location()->first()->location }}</div> <div style="width:280px; clear:both; border-top:1px solid #ddd; margin-top:10px; padding-top:5px">{{ \str_limit($item->description,150,'...') }} <br /><br /> <a href="{{ URL::to('items').'/'.$item->id }}">{{ __('More...') }}</a></div></div>', {{ $item->location()->first()->lat }}, {{ $item->location()->first()->lng }}, {{ $itemCount }}],
                    @php($itemCount--)
                    @endforeach
                ];

                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 13,
                    center: new google.maps.LatLng(lat, lng),
                    mapTypeId: google.maps.MapTypeId.HYBRID
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

                reverseGeolocation(lat, lng);
                loadNearbyItems(lat,lng);
                $('#current-location-header').show();
                $('#we-could-determine-your-location').hide();

                // Change the cookie only if reload was triggered
                if(reload) {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL::to('/') }}/locations/save-location-cookie",
                        data: {
                            lat: lat,
                            lng: lng,
                            _token: "{{ csrf_token() }}",
                        }
                    });
                }
            }

        </script>

    </div>

    <div class="container-fluid map-container">
        <div id="new-item-link"><a href="{{ URL::to('items/create') }}" title="{{ __('Have you lost or found something? Submit it!') }}" class="add-new-item">{{ __('Have you lost or found something? Submit it!') }}</a></div>
        <div id="map"></div>
    </div>

    <div class="container">

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 latest-items">

            <h2>Latest items</h2>
            <div class="subtitle">These are the latest lost or found items in your area.</div>

            <div id="latest-item-list"><img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading animation" id="ajax-loader-anim" /></div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 notification-form">

            <h2>Notifications in your mailbox!</h2>
            <div class="subtitle">You will be immediately notified when someone losts or finds and item in your area.</div>

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
                    {!! Form::label('distance', __('Distance in KM')) !!} <span class="required">*</span>
                </div>
                <div class="col-xs-12" id="slidercontainer">
                    <input type="range" min="0.5" max="300" value="5" step="0.5" class="slider" id="distance" name="distance">
                </div>

                <div id="distance-text"></div>

                <div class="col-md-12 submit-button-container text-center">
                    {!! Form::submit(__('Save notification'), ['class="form-control btn btn-primary"']) !!}
                </div>

                {!! Form::hidden('location_lat', '', ['id="location_lat"']) !!}
                {!! Form::hidden('location_lng', '', ['id="location_lng"']) !!}

                {!! Form::token() !!}

            {!! Form::close() !!}

        </div>

        <script>
            google.maps.event.addDomListener(window, 'load', function () {
                var places = new google.maps.places.Autocomplete(document.getElementById('location'));
                google.maps.event.addListener(places, 'place_changed', function () {
                    var place = places.getPlace();
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

    </div>

@endsection