@if($isImage)
    
    <p>
        <img src="{{ $fileModel->thumbnail($width, $height, $filters) }}" class="{{ $classNames ?? "" }}" alt="">
    </p>

@else
    
    <p class="sharp-file {{ $classNames ?? '' }}">
        <svg class="sharp-file__icon" width="1.5em" height="1.5em" style="vertical-align: -0.25em;" viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6m4 18H6V4h7v5h5v11z" fill="#626262"/>
        </svg>
        
        <small class="sharp-file__name" style="font-weight: 600">
            {{ basename($fileModel->file_name) }}
        </small>
    </p>

@endif
