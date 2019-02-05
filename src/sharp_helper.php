<?php

/**
 * @return string
 */
function sharp_version()
{
    return \Code16\Sharp\SharpServiceProvider::VERSION;
}

/**
 * @param array $sharpMenu
 * @param string|null $entityKey
 * @return string
 */
function sharp_page_title($sharpMenu, $entityKey)
{
    $title = "";

    if(request()->is(sharp_base_url_segment() . "/login")) {
        $title = trans('sharp::login.login_page_title');

    } elseif ($sharpMenu) {
        $menuItems = collect($sharpMenu->menuItems);

        // Handle Multiforms
        $entityKey = explode(':', $entityKey)[0];

        $label = $menuItems
                ->where('type', 'entity')
                ->firstWhere('key', $entityKey)
                ->label ?? "";

        if(!$label) {
            $label = $menuItems
                    ->where('type', 'category')
                    ->pluck('entities')
                    ->flatten()
                    ->firstWhere('key', $entityKey)
                    ->label ?? "";
        }

        $title = $sharpMenu->name . ($label ? ', ' . $label : '');
    }

    return config("sharp.display_sharp_version_in_title", true)
        ? "$title | Sharp " . sharp_version()
        : $title;
}

/**
 * @return mixed
 */
function sharp_user()
{
    return auth()->user();
}

/**
 * @param string $ability
 * @param string $entityKey
 * @param string|null $instanceId
 * @return bool
 */
function sharp_has_ability(string $ability, string $entityKey, string $instanceId = null)
{
    try {
        sharp_check_ability($ability, $entityKey, $instanceId);
        return true;

    } catch(Code16\Sharp\Exceptions\Auth\SharpAuthorizationException $ex){
        return false;
    }
}

/**
 * @param string $ability
 * @param string $entityKey
 * @param string|null $instanceId
 * @throws Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
 */
function sharp_check_ability(string $ability, string $entityKey, string $instanceId = null)
{
    app(Code16\Sharp\Auth\SharpAuthorizationManager::class)
        ->check($ability, $entityKey, $instanceId);
}

/**
 * Replace embedded images with thumbnails in a SharpMarkdownField's markdown text.
 *
 * @param string $html
 * @param string $classNames
 * @param int|null $width
 * @param int|null $height
 * @param array $filters
 * @return string
 */
function sharp_markdown_thumbnails(string $html, string $classNames, int $width = null, int $height = null, array $filters = [])
{
    preg_match_all('/<img src="(.*)".*>/U', $html, $matches, PREG_SET_ORDER);

    foreach($matches as $match) {
        list($disk, $file_name) = explode(":", $match[1]);

        $model = new Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel(compact('disk', 'file_name'));

        $html = str_replace(
            $match[0],
            sprintf('<img src="%s" class="%s" alt="">', $model->thumbnail($width, $height, $filters), $classNames),
            $html
        );
    }

    return $html;
}

/**
 * Include <script> tag for sharp plugins if available.
 *
 * @return string
 */
function sharp_custom_form_fields()
{
    if(config("sharp.extensions.activate_custom_form_fields", false)) {
        try {
            return "<script src='" . mix('/js/sharp-plugin.js') . "'></script>";
        } catch (\Exception $forget) {}
    }

    return "";
}

/**
 * Return true if current Laravel installation is newer than
 * given version (ex: 5.6).
 *
 * @param string $version
 * @return bool
 */
function sharp_laravel_version_gte($version)
{
    list($major, $minor) = explode(".", $version);
    list($laravelMajor, $laravelMinor, $bugfix) = explode(".", app()::VERSION);

    return $laravelMajor > $major
        || ($laravelMajor == $major && $laravelMinor >= $minor);
}

/**
 * @return string
 */
function sharp_base_url_segment()
{
    return config("sharp.custom_url_segment", "sharp");
}