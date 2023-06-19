<?php

namespace App\Sharp\Profile\Commands;

use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class UpdateProfilePasswordCommand extends SingleInstanceCommand
{

    public function label(): ?string
    {
        return 'Update password...';
    }
    
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('password')
                    ->setLabel('Current password')
                    ->setInputTypePassword()
            )
            ->addField(
                SharpFormTextField::make('new_password')
                    ->setLabel('New password')
                    ->setInputTypePassword()
            )
            ->addField(
                SharpFormTextField::make('new_password_confirmation')
                    ->setLabel('Confirm new password')
                    ->setInputTypePassword()
            );
    }

    protected function executeSingle(array $data): array
    {
        $this->validate($data, [
            'password' => 'required',
            'new_password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);
        
        $granted = auth()->validate([
            'email' => auth()->user()->email,
            'password' => $data['password'],
        ]);
        
        if(!$granted) {
            throw new SharpApplicativeException('Your current password is invalid.');
        }

        $this->notify('Password updated!');
        
        return $this->reload();
    }
}