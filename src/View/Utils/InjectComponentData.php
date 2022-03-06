<?php

namespace Code16\Sharp\View\Utils;

use Illuminate\Support\Facades\Blade;

trait InjectComponentData
{
    protected function getParentData(array $keys): array
    {
        $parentData = [];
        $template = sprintf('@aware(%s) @php $getData(get_defined_vars()) @endphp',
            var_export($keys, true),
        );
        Blade::render($template, [
            'getData' => function ($data) use ($keys, &$parentData) {
                foreach ($keys as $key) {
                    $parentData[$key] = $data[$key];
                }
            },
        ]);

        return $parentData;
    }

    protected function aware($key)
    {
        $data = $this->getParentData([$key]);

        return $data[$key];
    }
}
