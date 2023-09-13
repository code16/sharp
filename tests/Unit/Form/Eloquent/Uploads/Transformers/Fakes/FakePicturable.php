<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Uploads\Transformers\Fakes;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FakePicturable extends Model
{
    protected $guarded = [];

    public function picture(): MorphOne
    {
        return $this->morphOne(SharpUploadModel::class, 'model');
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(SharpUploadModel::class, 'model');
    }
}