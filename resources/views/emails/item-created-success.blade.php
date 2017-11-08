<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('Lost and Found') }}</title>
    <![endif]-->
</head>

<body>

@if(isset($additionalActivationMessage))
    {{ $additionalActivationMessage }}<br /><br />
@endif

{{ __('Meanwhile, here is your link for editing or deleting your item:') }}<br />
<br />
<a href="{{ $itemActionsLink }}">{{ $itemActionsLink }}</a><br />
<br />
{{ __('Don\'t share it with anyone!') }}<br />

</body>
</html>