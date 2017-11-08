@extends('layout')

@section('main')

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
            <a href="{{ URL::to('items/moderate/'.$item->id.'/'.$item->unique_id.'/'.$item->admin_hash.'/delete') }}"><button class="btn btn-danger">{{ __('Delete item') }}</button></a>
            <a href="{{ URL::to('items/moderate/'.$item->id.'/'.$item->unique_id.'/'.$item->admin_hash.'/activate') }}"><button class="btn btn-danger">{{ __('Activate item') }}</button></a>
        </div>
    @endif

    @endif

@endsection