<?php

namespace App\Sharp\Profile\Commands;

use Code16\Sharp\Auth\Password\Command\IsChangePasswordCommandTrait;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Illuminate\Validation\Rules\Password;

class ChangePasswordCommand extends SingleInstanceCommand
{
    use IsChangePasswordCommandTrait;

    public function buildCommandConfig(): void
    {
        $this->configureConfirmPassword()
            ->configurePasswordRule(
                Password::min(8)
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            );
    }

    protected function executeSingle(array $data): array
    {
        // We do not really update the password in the context of the demo
        //        auth()->user()->update([
        //            'password' => $data['password'],
        //        ]);

        $this->notify('Password updated!');

        return $this->reload();
    }
}
