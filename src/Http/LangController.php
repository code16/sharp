<?php

namespace Code16\Sharp\Http;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class LangController extends Controller
{
    /**
     * Echoes out the localization messages as a JS file,
     * to be used by the front code (Vue.js).
     */
    public function index()
    {
        $lang = app()->getLocale();
        $version = sharp_version();

        $strings = Cache::rememberForever("sharp.lang.$lang.$version.js", function () {
            $localizationStrings = [];
            $files = [
                'action_bar',
                'dashboard',
                'entity_list',
                'form',
                'modals',
                'show',
            ];

            collect($files)
                ->map(function (string $filename) {
                    return collect(trans("sharp-front::$filename"))
                        ->mapWithKeys(function ($value, $key) use ($filename) {
                            return ["$filename.$key" => $value];
                        })
                        ->toArray();
                })
                ->each(function (array $values) use (&$localizationStrings) {
                    $localizationStrings += $values;
                });

            return $localizationStrings;
        });

        return response('window.i18n = '.json_encode($strings).';')
            ->header('Content-Type', 'application/javascript');
    }
}
