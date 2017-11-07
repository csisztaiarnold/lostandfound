<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('Lost and Found') }}</title>
    <![endif]-->
</head>

<body>

{{ __('A new item has been :type in your area', ['type' => $type]) }}<br />
<br />
<a href="{{ $item_link }}">{{ $item_link }}</a><br />

<h1>{{ $item_title }}</h1>
{{ $item_description }}

</body>
</html>