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

        // TestResponse was renamed in Laravel 7.0
        if(class_exists("Illuminate\Testing\TestResponse")) {
            $testResponseClass = "Illuminate\Testing\TestResponse";
        } else {
            $testResponseClass = "Illuminate\Foundation\Testing\TestResponse";
        }
        
        $response = $testResponseClass::fromBaseResponse(
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