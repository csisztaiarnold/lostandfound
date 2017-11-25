@extends('layout')

@section('main')

    <div class="container item-page">

    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if(isset($item->title))

        <h2>
            <strong>
                @if($item->type === 'lost')
                    {{ __('Lost') }}:
                @else
                    {{ __('Found') }}:
                @endif
            </strong>
            {{ $item->title }}
        </h2>

        <div class="location">
            {{ $item->location }}
        </div>

        <div class="description">
            {!! nl2br(e($item->description)) !!}
        </div>

        @if(count($images) > 0)
            <div class="images">
            @foreach($images as $image)
                <a href="#" data-featherlight="{{ URL::to('/item_images') }}/{{ $item->id }}/{{ $image->filename }}.{{ $image->extension }}" class="gallery"><img src="{{ URL::to('/item_images') }}/{{ $item->id }}/{{ $image->filename }}_thumb.{{ $image->extension }}" /></a>
            @endforeach
            </div>
        @endif

        <div id="mapLocation"></div>
        <script>

            var latLng = new google.maps.LatLng({{ $location->lat }}, {{ $location->lng }});
            var map = new google.maps.Map(document.getElementById('mapLocation'), {
                zoom: 17,
                center: latLng,
                mapTypeId: google.maps.MapTypeId.HYBRID
            });

            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
            });

        </script>

        @if($moderation === true)
            <div class="moderation">
                <a href="{{ URL::to('items/'.$item->id.'/delete') }}"><button class="btn btn-danger">{{ __('Delete item') }}</button></a>
                @if($item->active === 1 || $item->active === '1')
                    <a href="{{ URL::to('items/'.$item->id.'/deactivate') }}"><button class="btn btn-danger">{{ __('Dectivate item') }}</button></a>
                @else
                    <a href="{{ URL::to('items/'.$item->id.'/activate') }}"><button class="btn btn-danger">{{ __('Activate item') }}</button></a>@endif
            </div>
        @endif

    @else
        {{ __('Sorry, nothing here') }}
    @endif

    </div>
@endsection