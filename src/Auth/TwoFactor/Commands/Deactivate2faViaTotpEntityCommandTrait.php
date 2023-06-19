<?php

namespace Code16\Sharp\Auth\TwoFactor\Commands;

trait Deactivate2faViaTotpEntityCommandTrait
{
    use Deactivate2faViaTotpCommon;
    
    public function execute(array $data = []): array
    {
        return $this->executeSingleOrEntity($data);
    }
}
