<?php

namespace Code16\Sharp\Auth\TwoFactor\Commands;

use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;

trait Deactivate2faViaTotpSingleInstanceCommandTrait
{
    use Deactivate2faViaTotpCommon;

    public function executeSingle(array $data = []): CommandReturn
    {
        return $this->executeSingleOrEntity($data);
    }
}
