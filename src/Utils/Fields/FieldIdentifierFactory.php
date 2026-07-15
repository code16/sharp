<?php

namespace Code16\Sharp\Utils\Fields;

use Illuminate\Support\Facades\Crypt;

class FieldIdentifierFactory
{
    public function form($instanceId): FieldIdentifier
    {
        return new FieldIdentifier('form', ['instanceId' => $instanceId]);
    }

    public function show($instanceId): FieldIdentifier
    {
        return new FieldIdentifier('show', ['instanceId' => $instanceId]);
    }

    public function embed(string $embedKey): FieldIdentifier
    {
        return new FieldIdentifier('embed', ['embedKey' => $embedKey]);
    }

    protected function encrypt(FieldIdentifier $identifier): string
    {
        return Crypt::encrypt($identifier);
    }

    public function decrypt(string $encryptedIdentifier): FieldIdentifier
    {
        return Crypt::decrypt($encryptedIdentifier);
    }
}
