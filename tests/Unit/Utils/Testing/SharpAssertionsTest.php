<?php

namespace Code16\Sharp\Tests\Unit\Utils\Testing;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class SharpAssertionsTest extends SharpTestCase
{
    use SharpAssertions;
    
    /** @test */
    function we_can_assert_has_authorization()
    {
        $this->initSharpAssertions();
        
        $response = TestResponse::fromBaseResponse(
            JsonResponse::create([
                "authorizations" => [
                    "create" => true,
                    "update" => false
                ]
            ])
        );
        
        $response->assertSharpHasAuthorization("create");
        $response->assertSharpHasNotAuthorization("update");
    }
}