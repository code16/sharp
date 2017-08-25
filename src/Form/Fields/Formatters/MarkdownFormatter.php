<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class MarkdownFormatter implements SharpFieldFormatter
{
//    /**
//     * @var UploadFormatter
//     */
//    private $uploadFormatter;
//
//    /**
//     * @param UploadFormatter $uploadFormatter
//     */
//    public function __construct(UploadFormatter $uploadFormatter)
//    {
//        $this->uploadFormatter = $uploadFormatter;
//    }

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return [
            "text" => $value
        ];
    }

    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $value['text'];
    }

//    /**
//     * @param $value
//     * @param SharpFormMarkdownField $field
//     * @param Model $instance
//     * @return mixed
//     */
//    public function format($value, SharpFormMarkdownField $field, Model $instance)
//    {
//        if(isset($value["files"])) {
//            foreach($value["files"] as $file) {
//                dd($this->uploadFormatter->format($file, $field, $instance));
//            }
//        }
//
//        return $value;
//    }



}