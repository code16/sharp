<?php

namespace Code16\Sharp\Utils\Testing;

trait GeneratesCurrentPageUrl
{
    private function buildCurrentPageUrl(string $breadcrumbUri): string
    {
        return url(
            sprintf(
                '/%s/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                sharp()->context()->globalFilterUrlSegmentValue(),
                $breadcrumbUri
            )
        );
    }
}
