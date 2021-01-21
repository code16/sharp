<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Preview</title>

    <style>
        html, body {
            background-color: #ddd;
            color: #636b6f;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
            max-width: 450px;
            border: 2px solid #aaa;
            padding: 30px;
            background: #FFF;
        }

        .title {
            font-size: 32px;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="flex-center">

        <div class="content">
            <div class="title m-b-md">
                {{ $spaceship->name }}
            </div>
            
            {!! sharp_markdown_thumbnails((new \Parsedown())->text($spaceship->description), "test", 200) !!}
        </div>
    </div>
</body>
</html>
