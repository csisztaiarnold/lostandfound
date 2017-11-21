@extends('layout')

@section('main')

    <div class="container">

    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if(isset($item->title))

        <h2>{{ $item->title }}</h2>

        {{ $item->description }}

        <div class="col-xs-12">
            @if($images)
                @foreach($images as $image)
                    <img src="{{ URL::to('/item_images') }}/{{ $item->id }}/{{ $image->filename }}_thumb.{{ $image->extension }}" />
                @endforeach
            @endif
        </div>

        @if($moderation === true)
            <div class="col-xs-12">
                <a href="{{ URL::to('items/'.$item->id.'/delete') }}"><button class="btn btn-danger">{{ __('Delete item') }}</button></a>
                @if($item->active === 1)
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