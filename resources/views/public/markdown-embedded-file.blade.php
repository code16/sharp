@if(in_array(\Illuminate\Support\Facades\Storage::disk($fileModel->disk)->mimeType($fileModel->file_name), ['image/jpeg','image/gif','image/png','image/bmp']))
    
    <img src="{{ $model->thumbnail($width, $height, $filters) }}" class="{{ $classes ?? "" }}" alt="">

@else
    
    <div class="{{ $classes ?? "" }}" style="text-align:center; font-size: 1.5em; color: lightslategrey; background-color: #dddddd; padding: 1em; border-radius: .2em">
        {{ basename($fileModel->file_name) }}
    </div>
    
@endif