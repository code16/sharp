<?php

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

it('handles belongsTo', function () {
    $marie = Person::create([
        'name' => 'Marie Curie',
    ]);

    $pierre = Person::create([
        'name' => 'Pierre Curie',
        'partner_id' => $marie->id,
    ]);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this->transform(Person::with('partner')->find($id));
        }
    };

    expect($form->find($pierre->id))
        ->toHaveKey('partner_id', $marie->id)
        ->toHaveKey('partner.id', $marie->id)
        ->toHaveKey('partner.name', $marie->name);
})->group('eloquent');

it('handles hasOne', function () {
    $marie = Person::create([
        'name' => 'Marie Curie',
    ]);

    $director = Person::create([
        'name' => 'Arthur',
        'chief_id' => $marie->id,
    ]);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this->transform(Person::with('director')->find($id));
        }
    };

    expect($form->find($marie->id))
        ->toHaveKey('director.id', $director->id)
        ->toHaveKey('director.name', $director->name);
})->group('eloquent');

it('handles hasMany', function () {
    $marie = Person::create([
        'name' => 'Marie Curie',
    ]);

    $collaborator1 = Person::create([
        'name' => 'Arthur',
        'chief_id' => $marie->id,
    ]);

    $collaborator2 = Person::create([
        'name' => 'Jeanne',
        'chief_id' => $marie->id,
    ]);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this->transform(Person::with('collaborators')->find($id));
        }
    };

    expect($form->find($marie->id))
        ->toHaveKey('collaborators.0.id', $collaborator1->id)
        ->toHaveKey('collaborators.1.id', $collaborator2->id)
        ->and($form->find($marie->id)['collaborators'])->toHaveCount(2);
})->group('eloquent');

it('handles belongsToMany', function () {
    $marie = Person::create([
        'name' => 'Marie Curie',
    ]);

    $colleague1 = Person::create([
        'name' => 'Arthur',
    ]);

    $colleague2 = Person::create([
        'name' => 'Jeanne',
    ]);

    $marie->colleagues()->attach([
        $colleague1->id,
        $colleague2->id,
    ]);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this->transform(Person::with('colleagues')->find($id));
        }
    };

    expect($form->find($marie->id))
        ->toHaveKey('colleagues.0.id', $colleague1->id)
        ->toHaveKey('colleagues.1.id', $colleague2->id)
        ->and($form->find($marie->id)['colleagues'])->toHaveCount(2);
})->group('eloquent');

it('handles morphOne', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $marie->photo()->create(['file' => 'photo.jpg']);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this->transform(Person::with('photo')->find($id));
        }
    };

    expect($form->find($marie->id))
        ->toHaveKey('photo.file', 'photo.jpg');
})->group('eloquent');

it('handles morphMany', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $marie->pictures()->create(['file' => 'pic-1.jpg']);
    $marie->pictures()->create(['file' => 'pic-2.jpg']);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this->transform(Person::with('pictures')->find($id));
        }
    };

    expect($form->find($marie->id))
        ->toHaveKey('pictures.0.file', 'pic-1.jpg')
        ->toHaveKey('pictures.1.file', 'pic-2.jpg')
        ->and($form->find($marie->id)['pictures'])->toHaveCount(2);
})->group('eloquent');

it('handles the relation separator', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $pierre = Person::create(['name' => 'Pierre Curie', 'partner_id' => $marie->id]);

    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('partner:name'));
        }

        public function find(mixed $id): array
        {
            return $this->transform(Person::with('partner')->find($id));
        }
    };

    expect($form->find($pierre->id))
        ->toHaveKey('partner:name', $marie->name)
        ->and($form->find($marie->id))
        ->toHaveKey('partner:name', null);
})->group('eloquent');

it('allows to use a closure as a custom transformer', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this
                ->setCustomTransformer('name', fn ($name) => strtoupper($name))
                ->transform(Person::find($id));
        }
    };

    expect($form->find($marie->id))
        ->toHaveKey('name', 'MARIE CURIE');
});

it('allows to use applyIfAttributeIsMissing in a custom transformer', function () {
    $person = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
    {
        public function find(mixed $id): array
        {
            return $this
                ->setCustomTransformer('slug', new class() implements SharpAttributeTransformer
                {
                    public function apply($value, $instance = null, $attribute = null)
                    {
                        return str($instance->name)->slug();
                    }

                    public function applyIfAttributeIsMissing(): bool
                    {
                        return false;
                    }
                })
                ->setCustomTransformer('force_slug', new class() implements SharpAttributeTransformer
                {
                    public function apply($value, $instance = null, $attribute = null)
                    {
                        return str($instance->name)->slug();
                    }
                })
                ->transform(Person::find($id));
        }
    };

    expect($form->find($person->id))
        ->not->toHaveKey('slug')
        ->toHaveKey('force_slug', 'marie-curie');
});

it('allows to use add a custom transformer for a field in a list', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    Person::create(['name' => 'Arthur', 'chief_id' => $marie->id]);
    Person::create(['name' => 'Jeanne', 'chief_id' => $marie->id]);

    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormListField::make('collaborators')
                    ->addItemField(SharpFormTextField::make('name')),
            );
        }

        public function find(mixed $id): array
        {
            return $this
                ->setCustomTransformer('collaborators[name]', fn ($name) => strtoupper($name))
                ->transform(Person::with('collaborators')->find($id));
        }
    };

    expect($form->find($marie->id))
        ->toHaveKey('collaborators.0.name', 'ARTHUR')
        ->toHaveKey('collaborators.1.name', 'JEANNE');
});
