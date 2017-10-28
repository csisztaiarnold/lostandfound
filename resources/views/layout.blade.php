<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('Lost and Found') }}</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}?r=<?php echo rand(); ?>" rel="stylesheet">
    <script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
</head>
<body>

<div class="container">

    <header>

        <nav></nav>

    </header>

    <main>

        @yield('main')

    </main>

    <footer>

    </footer>

</div><!-- // Container -->

</body>
</html>