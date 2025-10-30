<?php

use Code16\Sharp\Auth\Password\Command\IsChangePasswordCommandTrait;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\SinglePersonShow;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Illuminate\Validation\Rules\Password;

beforeEach(function () {
    sharp()->config()->declareEntity(SinglePersonEntity::class);

    login(new User([
        'id' => 123, // ensure RateLimiter key is unique in tests
        'password' => Hash::make('secret'),
    ]));
});

it('exposes proper form fields and label (without confirmation) for change password command', function () {
    fakeShowFor(SinglePersonEntity::class, new class() extends SinglePersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'change_password' => new class() extends SingleInstanceCommand
                {
                    use IsChangePasswordCommandTrait;

                    protected function executeSingle(array $data): array
                    {
                        // no-op in tests
                        return $this->reload();
                    }
                },
            ];
        }
    });

    // Fetch the command form (single show variant)
    $this
        ->getJson(route('code16.sharp.api.show.command.singleInstance.form', [
            'entityKey' => 'single-person',
            'commandKey' => 'change_password',
        ]))
        ->assertOk()
        ->assertJson(function (Assert $json) {
            $json
                ->where('config.title', trans('sharp::auth.password_change.command.label'))
                ->where('fields.password.key', 'password')
                ->where('fields.new_password.key', 'new_password')
                ->missing('fields.new_password_confirmation')
                ->etc();
        });
});

it('shows confirmation field when enabled and enforces custom password rule and confirmation', function () {
    fakeShowFor(SinglePersonEntity::class, new class() extends SinglePersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                // enable confirmation + a stronger rule
                'change_password_confirm' => new class() extends SingleInstanceCommand
                {
                    use IsChangePasswordCommandTrait;

                    public function buildCommandConfig(): void
                    {
                        $this->configureConfirmPassword()
                            ->configurePasswordRule(Password::min(8)->numbers());
                    }

                    protected function executeSingle(array $data): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    // Form contains the confirmation field
    $this
        ->getJson(route('code16.sharp.api.show.command.singleInstance.form', [
            'entityKey' => 'single-person',
            'commandKey' => 'change_password_confirm',
        ]))
        ->assertOk()
        ->assertJson(function (Assert $json) {
            $json
                ->where('fields.password.key', 'password')
                ->where('fields.new_password.key', 'new_password')
                ->where('fields.new_password_confirmation.key', 'new_password_confirmation')
                ->etc();
        });

    // Fails when confirmation is missing/mismatch
    $this
        ->postJson(route('code16.sharp.api.show.command.instance', ['single-person', 'change_password_confirm']), [
            'data' => [
                'password' => 'secret',
                'new_password' => 'Password1', // missing confirmation
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['new_password']);

    // Fails when password rule is not satisfied (requires number)
    $this
        ->postJson(route('code16.sharp.api.show.command.instance', ['single-person', 'change_password_confirm']), [
            'data' => [
                'password' => 'secret',
                'new_password' => 'Password!',
                'new_password_confirmation' => 'Password!',
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['new_password']);

    // Succeeds with valid data
    $this
        ->postJson(route('code16.sharp.api.show.command.instance', ['single-person', 'change_password_confirm']), [
            'data' => [
                'password' => 'secret',
                'new_password' => 'Password1!',
                'new_password_confirmation' => 'Password1!',
            ],
        ])
        ->assertOk();
});

it('rate limits after too many attempts and returns a helpful message', function () {
    fakeShowFor(SinglePersonEntity::class, new class() extends SinglePersonShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'change_password_rl' => new class() extends SingleInstanceCommand
                {
                    use IsChangePasswordCommandTrait;

                    protected function executeSingle(array $data): array
                    {
                        return $this->reload();
                    }
                },
            ];
        }
    });

    // Trigger 3 attempts (invalid to keep trying)
    for ($i = 0; $i < 3; $i++) {
        $this
            ->postJson(route('code16.sharp.api.show.command.instance', ['single-person', 'change_password_rl']), [
                'data' => [
                    // missing fields triggers validation and consumes an attempt
                ],
            ])
            ->assertUnprocessable();
    }

    // 4th attempt should be blocked by rate limiter with SharpApplicativeException (417)
    $this
        ->postJson(route('code16.sharp.api.show.command.instance', ['single-person', 'change_password_rl']), [
            'data' => [
                // still invalid
            ],
        ])
        ->assertStatus(417)
        ->assertJson(function (Assert $json) {
            $json->where('message', function ($message) {
                return is_string($message) && str_starts_with($message, 'You have made too many attempts.');
            });
        });
});
