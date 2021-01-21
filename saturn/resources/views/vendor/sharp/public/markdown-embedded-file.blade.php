@if($isImage)

    <img src="{{ $fileModel->thumbnail($width, $height, $filters) }}" class="{{ $classNames ?? "" }}" alt="">

@else

    <div class="position-ref" style="text-align:center; color: lightslategrey; background-color: #dddddd; padding: 1em; border-radius: .2em">
        <div style="font-size: 1.3em">File: {{ basename($fileModel->file_name) }}</div>
        <div><a href="#">Download</a></div>
    </div>

@endif