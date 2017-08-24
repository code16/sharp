<?php

namespace Code16\Sharp\Form\Eloquent\Transformers;

use Code16\Sharp\Form\Transformers\FormMarkdownWithUploadsTransformer;

class FormMarkdownWithSharpUploadModelsTransformer extends FormMarkdownWithUploadsTransformer
{
    /**
     * @var string
     */
    private $uploadModelClass;

    /**
     * @param string $uploadModelClass
     */
    public function __construct(string $uploadModelClass)
    {
        $this->uploadModelClass = $uploadModelClass;
    }

    /**
     * Return the upload as an array with
     * ["name", "thumbnail", "size"]
     *
     * @param string $filename
     * @return array|null
     */
    function getUpload(string $filename)
    {
        $uploadModel = (new $this->uploadModelClass())
            ->where("model_type", get_class($this->instance))
            ->where("model_id", $this->instance->getKey())
            ->where("model_key", "markdown_" . $this->attribute)
            ->where("file_name", $filename)
            ->first();

        if(!$uploadModel) return null;

        return [
            "name" => $uploadModel->file_name,
            "size" => $uploadModel->size,
            "thumbnail" => $uploadModel->thumbnail(null, 150)
        ];
    }
}