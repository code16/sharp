<?php

namespace App\Sharp\Entities;

use App\Sharp\AccountSharpForm;
use App\Sharp\AccountSharpShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class AccountEntity extends SharpEntity
{
    protected bool $isSingle = true;
    protected ?string $show = AccountSharpShow::class;
    protected ?string $form = AccountSharpForm::class;
    protected string $label = "My account";
}