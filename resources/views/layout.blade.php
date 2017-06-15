<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sharp</title>
    <link rel="stylesheet" href="/vendor/sharp/sharp.css">
</head>
<body>
    <div id="glasspane">
        <div class="bx--loading-overlay">
            <div data-loading class="bx--loading">
                <svg class="bx--loading__svg" viewBox="-75 -75 150 150">
                    <circle cx="0" cy="0" r="37.5" />
                </svg>
            </div>
        </div>
    </div>

    @yield('content')

    <script src="/vendor/sharp/sharp.js"></script>
</body>
</html>