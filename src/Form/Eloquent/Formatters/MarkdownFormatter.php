<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Illuminate\Database\Eloquent\Model;

class MarkdownFormatter
{
    /**
     * @var UploadFormatter
     */
    private $uploadFormatter;

    /**
     * @param UploadFormatter $uploadFormatter
     */
    public function __construct(UploadFormatter $uploadFormatter)
    {
        $this->uploadFormatter = $uploadFormatter;
    }
    
    /**
     * @param $value
     * @param SharpFormMarkdownField $field
     * @param Model $instance
     * @return mixed
     */
    public function format($value, SharpFormMarkdownField $field, Model $instance)
    {
        if(isset($value["files"])) {
            foreach($value["files"] as $file) {
                dd($this->uploadFormatter->format($file, $field, $instance));
            }
        }
    }
}