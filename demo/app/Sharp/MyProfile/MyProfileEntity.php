<?php

namespace App\Sharp\MyProfile;

use Code16\Sharp\Utils\Entities\SharpEntity;

class MyProfileEntity extends SharpEntity
{
    protected bool $isSingle = true;
    protected ?string $show = MyProfileSingleShow::class;
    protected ?string $form = MyProfileSingleForm::class;
    protected ?string $policy = MyProfilePolicy::class;
    protected string $label = "My profile";

}
