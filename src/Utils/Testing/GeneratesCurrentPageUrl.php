<?php

namespace Code16\Sharp\Utils\Testing;

use Code16\Sharp\Utils\Links\BreadcrumbBuilder;

trait GeneratesCurrentPageUrl
{
    private function buildCurrentPageUrl(BreadcrumbBuilder $builder): string
    {
        return url(
            sprintf(
                '/%s/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                sharp()->context()->globalFilterUrlSegmentValue(),
                $builder->generateUri()
            )
        );
    }
}
