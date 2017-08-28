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

        $strings = Cache::rememberForever("sharp.lang.$lang.js", function() {
            $strings = [];

            foreach(["action_bar", "form", "modals", "entity_list"] as $filename) {
                $strings += collect(trans("sharp-front::$filename"))
                    ->mapWithKeys(function ($value, $key) use ($filename) {
                        return ["$filename.$key" => $value];
                    })->all();
            }

            return $strings;
        });

        header('Content-Type: text/javascript');
        return 'window.i18n = ' . json_encode($strings) . ';';
    }
}