<?php

function currentSharpRequest(): \Code16\Sharp\Http\Context\CurrentSharpRequest
{
    return app(\Code16\Sharp\Http\Context\CurrentSharpRequest::class);
}

function sharp_version(): string
{
    return \Code16\Sharp\SharpServiceProvider::VERSION;
}

/**
 * @return mixed
 */
function sharp_user()
{
    return auth()->user();
}

function sharp_has_ability(string $ability, string $entityKey, string $instanceId = null): bool
{
    try {
        sharp_check_ability($ability, $entityKey, $instanceId);
        return true;

    } catch(Code16\Sharp\Exceptions\Auth\SharpAuthorizationException $ex){
        return false;
    }
}

function sharp_check_ability(string $ability, string $entityKey, string $instanceId = null)
{
    app(Code16\Sharp\Auth\SharpAuthorizationManager::class)
        ->check($ability, $entityKey, $instanceId);
}

/** @deprecated  */
function sharp_markdown_thumbnails(string $html, string $classNames, int $width = null, int $height = null, array $filters = []): string
{
    return sharp_markdown_embedded_files($html, $classNames, $width, $height, $filters);
}

/**
 * Handle embedded images and files in a SharpMarkdownField's markdown text.
 */
function sharp_markdown_embedded_files(
    string $html, string $classNames, 
    int $width = null, int $height = null, array $filters = [], 
    string $viewName = 'public.markdown-embedded-file'): string
{
    preg_match_all('/<p><img src="(.*)".*><\/p>/U', $html, $matches, PREG_SET_ORDER);

    foreach($matches as $match) {
        list($disk, $file_name) = explode(":", $match[1]);

        $model = new Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel(compact('disk', 'file_name'));
        
        $disk = \Illuminate\Support\Facades\Storage::disk($model->disk);
        if($disk->exists($model->file_name)) {
            $html = str_replace(
                $match[0],
                view("sharp::$viewName", [
                    "fileModel" => $model,
                    "isImage" => in_array($disk->mimeType($model->file_name), ['image/jpeg','image/gif','image/png','image/bmp']),
                    "classNames" => $classNames,
                    "width" => $width,
                    "height" => $height,
                    "filters" => $filters,
                ]),
                $html
            );
        }
    }

    return $html;
}

/**
 * Include <script> tag for sharp plugins if available.
 *
 * @return string
 */
function sharp_custom_fields(): string
{
    if(config("sharp.extensions.activate_custom_fields", false)) {
        try {
            return "<script src='" . mix('/js/sharp-plugin.js') . "'></script>";
        } catch (\Exception $forget) {}
    }

    return "";
}

/**
 * Return true if current Laravel installation is newer than
 * given version (ex: 5.6).
 */
function sharp_laravel_version_gte(string $version): bool
{
    list($major, $minor) = explode(".", $version);
    list($laravelMajor, $laravelMinor, $bugfix) = explode(".", app()::VERSION);

    return $laravelMajor > $major
        || ($laravelMajor == $major && $laravelMinor >= $minor);
}

function sharp_base_url_segment(): string
{
    return config("sharp.custom_url_segment", "sharp");
}

/**
 * Return true if the $handler class actually implements the $methodName method;
 * return false if the method is defined as concrete in a super class and not overridden.
 */
function is_method_implemented_in_concrete_class($handler, string $methodName): bool
{
    try {
        $foo = new \ReflectionMethod(get_class($handler), $methodName);
        $declaringClass = $foo->getDeclaringClass()->getName();

        return $foo->getPrototype()->getDeclaringClass()->getName() !== $declaringClass;

    } catch (\ReflectionException $e) {
        return false;
    }
}

function sharp_assets_out_of_date(): bool
{
    $distManifest = file_get_contents(__DIR__ . '/../resources/assets/dist/mix-manifest.json');
    $publicManifest = file_get_contents(public_path('vendor/sharp/mix-manifest.json'));
    
    return $distManifest !== $publicManifest;
}
