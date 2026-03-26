<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\LaravelPasskeys\Models\Passkey as SpatiePasskey;
use Spatie\LaravelPasskeys\Support\Serializer;
use Webauthn\CredentialRecord;

class Passkey extends SpatiePasskey
{
    public function data(): Attribute
    {
        $serializer = Serializer::make();

        return new Attribute(
            get: fn (string $value) => $serializer->fromJson(
                $value,
                CredentialRecord::class,
            ),
            set: fn (CredentialRecord $value) => [
                'credential_id' => self::encodeCredentialId($value->publicKeyCredentialId),
                'data' => $serializer->toJson($value),
            ],
        );
    }
}
