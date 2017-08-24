<?php

namespace Code16\Sharp\Form\Transformers;

/**
 * Handles the transformation of a string value to a valid structure
 * expected by the Markdown front field, handling embedded uploads.
 */
abstract class FormMarkdownWithUploadsTransformer extends FormMarkdownTransformer
{

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param $instance
     * @param string $attribute
     * @return mixed
     */
    function apply($instance, string $attribute)
    {
        $array = parent::apply($instance, $attribute);

        $md = $instance->$attribute;

        foreach($this->extractEmbeddedUploads($md) as $filename) {
            $array["files"][] = $this->getUpload($filename);
        }

        return $array;
    }

    /**
     * Return the upload as an array with
     * ["name", "thumbnail", "size"]
     *
     * @param string $filename
     * @return array
     */
    abstract function getUpload(string $filename): array;

    protected function extractEmbeddedUploads(string $md)
    {
        preg_match_all(
            '/!\[[^\]]*\]\((?<filename>.*?)(?=\"|\))(?<optionalpart>\".*\")?\)/',
            $md, $matches, PREG_SET_ORDER, 0
        );

        return collect($matches)->map(function($match) {
            return trim($match["filename"]);
        })->all();
    }
}