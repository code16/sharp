@if($isImage)
    
    <img src="{{ $fileModel->thumbnail($width, $height, $filters) }}" class="{{ $classeNames ?? "" }}" alt="">

@else
    
    <div class="{{ $classeNames ?? "" }}" style="text-align:center; font-size: 1.5em; color: lightslategrey; background-color: #dddddd; padding: 1em; border-radius: .2em">
        {{ basename($fileModel->file_name) }}
    </div>
    
@endif