@extends('layout')

@section('main')

    <div class="container item-list-page">

        <h2>{{ __('List of items lost or found in') }} <span class="exact-location">{{ \Cookie::get('location_name') }}</span></h2>

        {!! Form::open(['url' => 'items','id' => 'change-type-form','method'=>'get']) !!}
            <select name="type" id="type">
                <option value="all" @if($type === 'all') selected="selected" @endif>{{ __('Lost or found') }}</option>
                <option value="lost" @if($type === 'lost') selected="selected" @endif>{{ __('Lost') }}</option>
                <option value="found" @if($type === 'found') selected="selected" @endif>{{ __('Found') }}</option>
            </select>
        {!! Form::close() !!}

        <script>
            $(document).ready(function(){
               $('#type').change(function(){
                  $('#change-type-form').submit();
               });
            });
        </script>
        @if(count($items) > 0)
            @foreach($items as $item)
                <article>
                    <h1>
                        @if($item->type === 'lost')
                            <strong>{{ __('Lost') }}:</strong>
                        @else
                            <strong>{{ __('Found') }}:</strong>
                        @endif
                        <a href="{{ URL::to('items') }}/{{ $item->item_id }}" title="{{ $item->title }}">{{ $item->title }}</a>
                    </h1>
                    <div class="location">{{ $item->location }}</div>
                    <div class="description">
                    @if($item->filename)
                        <a href="{{ URL::to('items') }}/{{ $item->item_id }}" title="{{ $item->title }}"><img src="{{ URL::to('/item_images') }}/{{ $item->item_id }}/{{ $item->filename }}_thumb.{{ $item->extension }}" width="100" style="float:left; margin-right:10px" class="item-image"  /></a>
                    @endif
                        {{ \str_limit($item->description,450,'...') }} <a href="{{ URL::to('items') }}/{{ $item->item_id }}" title="{{ $item->title }}">{{ __('More') }} &raquo;</a>
                    </div>
                </article>
            @endforeach
            {{  $items->appends(request()->input()) }}
        @else
            {{ __('No items in the database yet.') }}
        @endif

    </div>

@endsection