<?php

namespace App;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Support\Facades\Event;
use Spatie\Translatable\Events\TranslationHasBeenSet;
use Spatie\Translatable\HasTranslations;

class Media extends SharpUploadModel
{
    use HasTranslations;

    public $translatable = ['legend'];

    protected $table = "medias";

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        Event::listen(TranslationHasBeenSet::class, function (TranslationHasBeenSet $event) {
            $event->model->updateCustomProperty($event->key, $event->newValue);
            unset($event->model->attributes[$event->key]);
        });
    }
}