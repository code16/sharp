<?php

namespace App\Sharp\Profile;

use Code16\Sharp\Utils\Entities\SharpEntity;

class ProfileEntity extends SharpEntity
{
    protected bool $isSingle = true;
    protected ?string $show = ProfileSingleShow::class;
    protected ?string $form = ProfileSingleForm::class;
    protected string $label = 'My profile';
}
