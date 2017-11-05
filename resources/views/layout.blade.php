<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('Lost and Found') }}</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}?r=<?php echo rand(); ?>" rel="stylesheet">
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/sortable/html.sortable.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
</head>
<body>

<div class="container">

    <header>

        <h1>Lost and Found</h1>

        <nav>
            <ul>
                <li><a href="{{ URL::to('/') }}" title="{{ __('Home') }}">{{ __('Home') }}</a></li>
                <li><a href="{{ URL::to('items/') }}" title="{{ __('List items') }}">{{ __('List items') }}</a></li>
                <li><a href="{{ URL::to('items/create') }}" title="{{ __('Add new item') }}">{{ __('Add new item') }}</a></li>
            </ul>
        </nav>

    </header>

    <main>

        @yield('main')

    </main>

    <footer>

    </footer>

</div><!-- // Container -->

</body>
</html>