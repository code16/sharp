<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\MarkdownFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpFormMarkdownField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder,
        SharpFormFieldWithUpload,
        SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "markdown";

    const B = "bold";
    const I = "italic";
    const HIGHLIGHT = "highlight";
    const UL = "bullet-list";
    const OL = "ordered-list";
    const SEPARATOR = "|";
    const A = "link";
    const H1 = "heading-1";
    const H2 = "heading-2";
    const H3 = "heading-3";
    const CODE = "code";
    const QUOTE = "blockquote";
    const UPLOAD_IMAGE = "upload-image";
    const UPLOAD = "upload";
    const HR = "horizontal-rule";
    const TABLE = "table";
    const IFRAME = "iframe";
    const RAW_HTML = "html";
    const UNDO = "undo";
    const REDO = "redo";

    /** @deprecated use UPLOAD */ const DOC = "upload";

    protected int $minHeight = 200;
    protected ?int $maxHeight = null;
    protected array $toolbar = [
        self::B, self::I,
        self::SEPARATOR,
        self::UL,
        self::SEPARATOR,
        self::A,
    ];
    protected bool $showToolbar = true;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new MarkdownFormatter());
    }

    public function setHeight(int $height, int|null $maxHeight = null): self
    {
        $this->minHeight = $height;
        // Spec maxHeight:
        // null: same as minHeight;
        // 0: infinite;
        // int: a defined size.
        $this->maxHeight = $maxHeight === null
            ? $height
            : ($maxHeight === 0 ? null : $maxHeight);

        return $this;
    }

    public function setToolbar(array $toolbar): self
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    public function hideToolbar(): self
    {
        $this->showToolbar = false;

        return $this;
    }

    public function showToolbar(): self
    {
        $this->showToolbar = true;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            "minHeight" => "required|integer",
            "maxHeight" => "integer|nullable",
            "toolbar" => "array|nullable",
            "maxImageSize" => "numeric",
            "ratioX" => "integer|nullable",
            "ratioY" => "integer|nullable",
            "transformable" => "boolean",
            "transformableFileTypes" => "array",
            "transformKeepOriginal" => "boolean",
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray(
            array_merge(
                [
                    "minHeight" => $this->minHeight,
                    "maxHeight" => $this->maxHeight,
                    "toolbar" => $this->showToolbar ? $this->toolbar : null,
                    "placeholder" => $this->placeholder,
                    "localized" => $this->localized,
                    "innerComponents" => [
                        "upload" => $this->innerComponentUploadConfiguration()
                    ]
                ],
                $this->editorCustomConfiguration()
            )
        );
    }

    protected function innerComponentUploadConfiguration(): array
    {
        $array = [
            "maxFileSize" => $this->maxFileSize ?: 2,
            "transformable" => $this->transformable,
        ];

        if($this->cropRatio) {
            $array["ratioX"] = (int)$this->cropRatio[0];
            $array["ratioY"] = (int)$this->cropRatio[1];
            $array["transformKeepOriginal"] = $this->transformKeepOriginal;
            $array["transformableFileTypes"] = $this->transformableFileTypes;
        }
        
        if(!$this->fileFilter) {
            $this->setFileFilterImages();
        }
        $array["fileFilter"] = $this->fileFilter;

        return $array;
    }

    protected function editorCustomConfiguration(): array
    {
        return [
            "tightListsOnly" => config("sharp.markdown_editor.tight_lists_only"),
            "nl2br" => config("sharp.markdown_editor.nl2br"),
        ];
    }
}
