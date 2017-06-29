<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sharp</title>
    <link rel="stylesheet" href="/vendor/sharp/sharp.css">
    <link rel="stylesheet" href="/vendor/sharp/sharp-cms.css">
</head>
<body>
    <div id="glasspane">
        <div class="SharpLoading__overlay">
            <div data-loading class="SharpLoading__container">
                <svg class="SharpLoading__svg" viewBox="-75 -75 150 150">
                    <circle cx="0" cy="0" r="37.5" />
                </svg>
            </div>
        </div>
    </div>

    @yield('content')

    <script src="/vendor/sharp/lang.js?locale={{ app()->getLocale() }}"></script>
    <script src="/vendor/sharp/sharp.js"></script>
</body>
</html>