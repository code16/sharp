<?php

namespace Code16\Sharp\Auth\Passkeys\Entity;

use Code16\Sharp\Auth\Passkeys\Commands\UpdatePasskeyNameCommand;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Fields\EntityListBadgeField;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;

class PasskeyList extends SharpEntityList
{
    public function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
            )
            ->addField(
                EntityListBadgeField::make('usage')
                    ->setLabel('Usage')
            )
            ->addField(
                EntityListField::make('created_at')
                    ->setLabel('Creation date')
            )
            ->addField(
                EntityListField::make('last_used_at')
                    ->setLabel('Last used at')
            );
    }

    public function buildListConfig(): void
    {
        $this->configurePrimaryEntityCommand('add');
    }

    public function getEntityCommands(): ?array
    {
        return [
            'add' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'Add a passkey';
                }

                public function execute(array $data = []): array
                {
                    redirect()->setIntendedUrl(sharp()->context()->breadcrumb()->getCurrentSegmentUrl());

                    return $this->link(route('code16.sharp.passkeys.create'));
                }
            },
        ];
    }

    public function getInstanceCommands(): ?array
    {
        return [
            UpdatePasskeyNameCommand::class,
        ];
    }

    public function delete(mixed $id): void
    {
        $this->currentUser()->passKeys()->findOrFail($id)->delete();
    }

    public function getListData(): array|Arrayable
    {
        return $this
            ->setCustomTransformer('usage', function ($value, Model $passkey) {
                return $passkey->getKey() == request()->cookie('sharp_last_used_passkey')
                    ? 'Used in this browser'
                    : null;
            })
            ->transform(
                $this->currentUser()->passkeys
            );
    }

    protected function currentUser(): Authenticatable&HasPasskeys
    {
        /** @var Authenticatable&HasPasskeys $user */
        $user = auth()->user();

        return $user;
    }
}
