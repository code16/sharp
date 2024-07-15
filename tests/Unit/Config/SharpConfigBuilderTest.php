<?php

it('allows to set and get a config value', function () {
    sharpConfig()->setName('Test project')
        ->setCustomUrlSegment('test-sharp');

    expect(sharpConfig()->get('name'))->toBe('Test project')
        ->and(sharpConfig()->get('custom_url_segment'))->toBe('test-sharp');
});