<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('Lost and Found') }}</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/featherlight/featherlight.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/amigo.sorter.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}?r=<?php echo rand(); ?>" rel="stylesheet">
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/amigo.sorter-1.0.js') }}"></script>
    <script src="{{ asset('js/featherlight/featherlight.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext" rel="stylesheet">
</head>
<body>

<div class="container">

    <header>

        <h1><a href="{{ URL::to('/') }}" title="{{ __('Home') }}"><img src="{{ asset('img/logo-lostandfound.svg') }}" alt="Logo" class="img-responsive" /></a></h1>

    </header>

</div>

<main>

    @yield('main')

</main>

<footer>

</footer>


</body>
</html>