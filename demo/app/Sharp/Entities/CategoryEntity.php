<?php

namespace App\Sharp\Entities;

use App\Sharp\Categories\CategoryForm;
use App\Sharp\Categories\CategoryList;
use App\Sharp\Categories\CategoryShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class CategoryEntity extends SharpEntity
{
    protected ?string $list = CategoryList::class;
    protected ?string $show = CategoryShow::class;
    protected ?string $form = CategoryForm::class;
}
