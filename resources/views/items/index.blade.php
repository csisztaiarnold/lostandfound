@extends('layout')

@section('main')

    <h2>{{ __('List of items') }}</h2>

    @if(count($items) > 0)
        @foreach($items as $items)
            {{ $items->title }}<br />
        @endforeach
    @else
        {{ __('No items in the database yet.') }}
    @endif

@endsection