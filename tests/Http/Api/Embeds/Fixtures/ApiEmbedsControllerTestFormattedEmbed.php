<?php

namespace Code16\Sharp\Tests\Http\Api\Embeds\Fixtures;

class ApiEmbedsControllerTestFormattedEmbed extends ApiEmbedsControllerTestSimpleEmbed
{
    public static string $key = 'Code16.Sharp.Tests.Http.Api.Embeds.Fixtures.ApiEmbedsControllerTestFormattedEmbed';

    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        return [
            'text' => $data['text'],
            'formatted' => str($data['text'])->upper()->toString(),
            'form' => $isForm,
        ];
    }
}