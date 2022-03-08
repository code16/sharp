<?php

namespace App\Sharp\Entities;

use App\Sharp\Profile\ProfileSingleForm;
use App\Sharp\Profile\ProfileSingleShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class ProfileEntity extends SharpEntity
{
    protected bool $isSingle = true;
    protected ?string $show = ProfileSingleShow::class;
    protected ?string $form = ProfileSingleForm::class;
    protected string $label = 'My profile';
}
