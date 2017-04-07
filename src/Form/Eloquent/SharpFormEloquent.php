<?php

namespace Code16\Sharp\Form\Eloquent;

use Illuminate\Database\Eloquent\Model;

interface SharpFormEloquent
{
    function findModel($id): Model;
}