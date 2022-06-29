<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithEmbeds;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpFormEditorField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder,
        SharpFormFieldWithUpload,
        SharpFormFieldWithDataLocalization,
        SharpFormFieldWithEmbeds;

    const FIELD_TYPE = 'editor';

    /** @deprecated use UPLOAD */
    const DOC = 'upload';

    const B = 'bold';
    const I = 'italic';
    const HIGHLIGHT = 'highlight';
    const SMALL = 'small';
    const UL = 'bullet-list';
    const OL = 'ordered-list';
    const SEPARATOR = '|';
    const A = 'link';
    const H1 = 'heading-1';
    const H2 = 'heading-2';
    const H3 = 'heading-3';
    const CODE = 'code';
    const QUOTE = 'blockquote';
    const UPLOAD_IMAGE = 'upload-image';
    const UPLOAD = 'upload';
    const HR = 'horizontal-rule';
    const TABLE = 'table';
    const IFRAME = 'iframe';
    const RAW_HTML = 'html';
    const CODE_BLOCK = 'code-block';
    const SUP = 'superscript';
    const UNDO = 'undo';
    const REDO = 'redo';

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
    protected bool $renderAsMarkdown = false;
    protected bool $withoutParagraphs = false;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new EditorFormatter());
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

    public function setWithoutParagraphs(bool $withoutParagraphs = true): self
    {
        $this->withoutParagraphs = $withoutParagraphs;

        return $this;
    }

    public function setRenderContentAsMarkdown(bool $renderAsMarkdown = true): self
    {
        $this->renderAsMarkdown = $renderAsMarkdown;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'minHeight' => 'required|integer',
            'maxHeight' => 'integer|nullable',
            'toolbar' => 'array|nullable',
            'maxImageSize' => 'numeric',
            'ratioX' => 'integer|nullable',
            'ratioY' => 'integer|nullable',
            'transformable' => 'boolean',
            'transformableFileTypes' => 'array',
            'transformKeepOriginal' => 'boolean',
            'markdown' => 'boolean',
            'inline' => 'boolean',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray(
            array_merge(
                [
                    'minHeight' => $this->minHeight,
                    'maxHeight' => $this->maxHeight,
                    'toolbar' => $this->showToolbar ? $this->toolbar : null,
                    'placeholder' => $this->placeholder,
                    'localized' => $this->localized,
                    'markdown' => $this->renderAsMarkdown,
                    'inline' => $this->withoutParagraphs,
                    'embeds' => array_merge(
                        $this->innerComponentUploadConfiguration(),
                        $this->innerComponentEmbedsConfiguration()
                    ),
                ],
                $this->editorCustomConfiguration(),
            ),
        );
    }

    protected function innerComponentUploadConfiguration(): array
    {
        $uploadConfig = [
            'maxFileSize' => $this->maxFileSize ?: 2,
            'transformable' => $this->transformable,
        ];

        if ($this->cropRatio) {
            $uploadConfig['ratioX'] = (int) $this->cropRatio[0];
            $uploadConfig['ratioY'] = (int) $this->cropRatio[1];
            $uploadConfig['transformKeepOriginal'] = $this->transformKeepOriginal;
            $uploadConfig['transformableFileTypes'] = $this->transformableFileTypes;
        }

        if (! $this->fileFilter) {
            $this->setFileFilterImages();
        }
        $uploadConfig['fileFilter'] = $this->fileFilter;

        return ['upload' => $uploadConfig];
    }

    protected function editorCustomConfiguration(): array
    {
        return $this->renderAsMarkdown
            ? [
                'tightListsOnly' => config('sharp.markdown_editor.tight_lists_only', true),
                'nl2br' => config('sharp.markdown_editor.nl2br', false),
            ]
            : [];
    }
}
