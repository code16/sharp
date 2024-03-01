<?php

namespace Code16\Sharp\Form\Fields;

use Closure;
use Code16\Sharp\Enums\FormEditorToolbarButton;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\Editor\Uploads\FormEditorUploadForm;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithEmbeds;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithMaxLength;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormEditorField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;
    use SharpFormFieldWithDataLocalization;
    use SharpFormFieldWithEmbeds;
    use SharpFormFieldWithMaxLength {
        setMaxLength as protected parentSetMaxLength;
    }

    const FIELD_TYPE = 'editor';

    const B = FormEditorToolbarButton::Bold;
    const I = FormEditorToolbarButton::Italic;
    const HIGHLIGHT = FormEditorToolbarButton::Highlight;
    const SMALL = FormEditorToolbarButton::Small;
    const UL = FormEditorToolbarButton::BulletList;
    const OL = FormEditorToolbarButton::OrderedList;
    const SEPARATOR = FormEditorToolbarButton::Separator;
    const A = FormEditorToolbarButton::Link;
    const H1 = FormEditorToolbarButton::Heading1;
    const H2 = FormEditorToolbarButton::Heading2;
    const H3 = FormEditorToolbarButton::Heading3;
    const CODE = FormEditorToolbarButton::Code;
    const QUOTE = FormEditorToolbarButton::Blockquote;
    const UPLOAD_IMAGE = FormEditorToolbarButton::UploadImage;
    const UPLOAD = FormEditorToolbarButton::Upload;
    const HR = FormEditorToolbarButton::HorizontalRule;
    const TABLE = FormEditorToolbarButton::Table;
    const IFRAME = FormEditorToolbarButton::Iframe;
    const RAW_HTML = FormEditorToolbarButton::Html;
    const CODE_BLOCK = FormEditorToolbarButton::CodeBlock;
    const SUP = FormEditorToolbarButton::Superscript;
    const UNDO = FormEditorToolbarButton::Undo;
    const REDO = FormEditorToolbarButton::Redo;

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
    protected bool $showCharacterCount = false;

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

    public function showCharacterCount(bool $showCharacterCount = true): self
    {
        $this->showCharacterCount = $showCharacterCount;

        return $this;
    }

    public function setMaxLength(int $maxLength): self
    {
        $this->showCharacterCount = true;

        return $this->parentSetMaxLength($maxLength);
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

    /**
     * @param  Closure(SharpFormEditorUpload):mixed  $callback
     */
    public function allowUploads(Closure $callback): self
    {
        $this->uploadsConfig = SharpFormEditorUpload::make('file');

        $callback($this->uploadsConfig);

        return $this;
    }

    public function uploadsConfig(): ?SharpFormEditorUpload
    {
        return $this->uploadsConfig;
    }

    protected function innerComponentUploadsConfiguration(): array
    {
        if (! $this->uploadsConfig) {
            return [];
        }

        $form = new FormEditorUploadForm($this->uploadsConfig);

        return [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
        ];
    }

    protected function toolbarArray(): ?array
    {
        if (! $this->showToolbar) {
            return null;
        }

        return collect($this->toolbar)
            ->map(function (FormEditorToolbarButton|string $button) {
                if (is_string($button)) {
                    if (in_array($button, $this->embeds)) {
                        return 'embed:'.app($button)->key();
                    }

                    throw new SharpInvalidConfigException(
                        sprintf('%s ("%s") : %s must be present in ->allowEmbeds() array to have it in the toolbar',
                            class_basename($this),
                            $this->key(),
                            $button
                        )
                    );
                }

                if (($button === static::UPLOAD || $button === static::UPLOAD_IMAGE) && ! $this->uploadsConfig()) {
                    throw new SharpInvalidConfigException(
                        sprintf('%s ("%s") : ->allowUploads() must be called to have upload in the toolbar',
                            class_basename($this),
                            $this->key(),
                        )
                    );
                }

                return $button;
            })
            ->toArray();
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
            'showCharacterCount' => 'boolean',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray(
            [
                'minHeight' => $this->minHeight,
                'maxHeight' => $this->maxHeight,
                'toolbar' => $this->toolbarArray(),
                'placeholder' => $this->placeholder,
                'localized' => $this->localized,
                'markdown' => $this->renderAsMarkdown,
                'inline' => $this->withoutParagraphs,
                'showCharacterCount' => $this->showCharacterCount,
                'maxLength' => $this->maxLength,
                'uploads' => $this->innerComponentUploadsConfiguration(),
                'embeds' => $this->innerComponentEmbedsConfiguration(),
            ],
        );
    }
}
