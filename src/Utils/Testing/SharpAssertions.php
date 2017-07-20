<?php

namespace Code16\Sharp\Utils\Testing;

use Illuminate\Foundation\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

trait SharpAssertions
{

    /**
     * This function must be called before using Sharp's assertions
     * (in the setUp() for instance)
     */
    protected function initSharpAssertions()
    {
        TestResponse::macro('assertSharpHasAuthorization', function ($authorization) {
            return $this->assertJson(
                ["authorizations" => [$authorization => true]]
            );
        });

        TestResponse::macro('assertSharpHasNotAuthorization', function ($authorization) {
            return $this->assertJson(
                ["authorizations" => [$authorization => false]]
            );
        });

        TestResponse::macro('assertSharpFormHasFieldOfType', function ($name, $formFieldClassName) {
            $type = $formFieldClassName::FIELD_TYPE;

            $this->assertJson(
                ["fields" => [$name => ["key" => $name, "type" => $type]]]
            );

            $this->assertSharpFormHasFields($name);

            return $this;
        });

        TestResponse::macro('assertSharpFormHasFields', function ($names) {

            foreach((array)$names as $name) {

                $this->assertJson(
                    ["fields" => [$name => ["key" => $name]]]
                );

                $found = false;

                foreach ($this->decodeResponseJson()["layout"]["tabs"] as $tab) {
                    foreach ($tab["columns"] as $column) {
                        foreach ($column["fields"] as $fieldset) {
                            foreach ($fieldset as $field) {
                                if(isset($field["legend"])) {
                                    foreach($field["fields"] as $fieldsetFields) {
                                        foreach($fieldsetFields as $field) {
                                            if ($field["key"] == $name) {
                                                $found = true;
                                                break 6;
                                            }
                                        }
                                    }
                                } else if ($field["key"] == $name) {
                                    $found = true;
                                    break 4;
                                }
                            }
                        }
                    }
                }

                if(!$found) {
                    PHPUnit::fail("The field [$name] was not found on the layout part.");
                }
            }

            return $this;
        });

        TestResponse::macro('assertSharpFormDataEquals', function ($name, $value) {
            return $this->assertJson(
                ["data" => [$name => $value]]
            );
        });
    }

    /**
     * @param string $entityKey
     * @param mixed|null $instanceId
     * @return mixed
     */
    protected function getSharpForm(string $entityKey, $instanceId = null)
    {
        return $this->json(
            'GET',
            $instanceId
                ? route("code16.sharp.api.form.edit", [$entityKey, $instanceId])
                : route("code16.sharp.api.form.create", $entityKey)
        );
    }

    /**
     * @param string $entityKey
     * @param $instanceId
     * @param array $data
     * @return mixed
     */
    protected function updateSharpForm(string $entityKey, $instanceId, array $data)
    {
        return $this->json(
            'POST', route("code16.sharp.api.form.update", [$entityKey, $instanceId]),
            $data
        );
    }

    /**
     * @param string $entityKey
     * @param array $data
     * @return mixed
     */
    protected function storeSharpForm(string $entityKey, array $data)
    {
        return $this->json(
            'POST', route("code16.sharp.api.form.store", $entityKey),
            $data
        );
    }

    /**
     * @param $user
     */
    protected function loginAsSharpUser($user)
    {
        $this->actingAs($user, config("sharp.auth.guard", config("auth.defaults.guard")));
    }
}