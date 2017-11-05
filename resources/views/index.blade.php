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

@endsection