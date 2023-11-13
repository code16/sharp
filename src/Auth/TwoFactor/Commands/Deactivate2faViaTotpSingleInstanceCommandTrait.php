<?php

namespace Code16\Sharp\Auth\TwoFactor\Commands;

trait Deactivate2faViaTotpSingleInstanceCommandTrait
{
    use Deactivate2faViaTotpCommon;

    public function executeSingle(array $data = []): array
    {
        return $this->executeSingleOrEntity($data);
    }
}
