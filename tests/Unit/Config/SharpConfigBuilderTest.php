<?php

it('allows to set and get a config value', function () {
    sharp()->config()->setName('Test project')
        ->setCustomUrlSegment('test-sharp');

    expect(sharp()->config()->get('name'))->toBe('Test project')
        ->and(sharp()->config()->get('custom_url_segment'))->toBe('test-sharp');
});