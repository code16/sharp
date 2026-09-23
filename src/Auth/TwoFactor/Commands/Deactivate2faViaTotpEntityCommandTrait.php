<?php

namespace Code16\Sharp\Auth\TwoFactor\Commands;

use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;

trait Deactivate2faViaTotpEntityCommandTrait
{
    use Deactivate2faViaTotpCommon;

    public function execute(array $data = []): CommandReturn
    {
        return $this->executeSingleOrEntity($data);
    }
}
