<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Enums\FormEditorToolbarButton;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\Editor\TextInputReplacement\EditorTextInputReplacement;
use Code16\Sharp\Form\Fields\Editor\TextInputReplacement\EditorTextInputReplacementPreset;
use Code16\Sharp\Form\Fields\Editor\Uploads\FormEditorUploadForm;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithMaxLength;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithEmbeds;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use Code16\Sharp\Utils\Fields\SharpFieldWithEmbeds;
use Code16\Sharp\Utils\Fields\SharpFieldWithLocalization;
use Code16\Sharp\Utils\Sanitization\IsSharpFieldWithHtmlSanitization;
use Code16\Sharp\Utils\Sanitization\SharpFieldWithHtmlSanitization;

class SharpFormEditorField extends SharpFormField implements IsSharpFieldWithEmbeds, IsSharpFieldWithHtmlSanitization, IsSharpFieldWithLocalization
{
    use SharpFieldWithEmbeds;
    use SharpFieldWithHtmlSanitization;
    use SharpFieldWithLocalization;
    use SharpFormFieldWithMaxLength {
        setMaxLength as protected parentSetMaxLength;
    }
    use SharpFormFieldWithPlaceholder;

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
    protected bool $allowFullscreen = false;
    protected array $textInputReplacements = [];

    protected function __construct(string $key, string $type, ?SharpFieldFormatter $formatter = null)
    {
        parent::__construct($key, $type, $formatter);
        $this->sanitizeHtml = true;
    }

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new EditorFormatter());
    }

    public function setHeight(int $height, ?int $maxHeight = null): self
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

    public function setTextInputReplacements(array $replacements): self
    {
        $this->textInputReplacements = $replacements;

        return $this;
    }

    public function allowFullscreen(bool $allowFullscreen = true): self
    {
        $this->allowFullscreen = $allowFullscreen;

        return $this;
    }

    public function allowUploads(SharpFormEditorUpload $formEditorUpload): self
    {
        $this->uploadsConfig = $formEditorUpload;

        return $this;
    }

    public function uploadsConfig(): ?SharpFormEditorUpload
    {
        return $this->uploadsConfig;
    }

    protected function innerComponentUploadsConfiguration(): ?array
    {
        if (! $this->uploadsConfig) {
            return null;
        }

        $form = new FormEditorUploadForm($this->uploadsConfig);

        return [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
        ];
    }

    /**
     * @internal
     */
    public function getToolbar(): array
    {
        return $this->toolbar;
    }

    protected function toolbarArray(): ?array
    {
        if (! $this->showToolbar) {
            return null;
        }

        return collect($this->toolbar)
            ->map(function (FormEditorToolbarButton|string $button) {
                if (is_string($button) && class_exists($button)) {
                    if ($embed = $this->getAllowedEmbed($button)) {
                        if (! $embed->toConfigArray(true)['icon']) {
                            throw new SharpInvalidConfigException(
                                sprintf('%s ("%s") : %s must have an icon to be in the toolbar',
                                    class_basename($this),
                                    $this->key(),
                                    $button
                                )
                            );
                        }

                        return 'embed:'.$embed->key();
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
                'allowFullscreen' => $this->allowFullscreen,
                'textInputReplacements' => collect($this->textInputReplacements)
                    ->flatMap(fn (EditorTextInputReplacement|EditorTextInputReplacementPreset $replacement) => $replacement instanceof EditorTextInputReplacementPreset
                            ? $replacement->toArray()
                            : [$replacement]
                    )
                    ->all(),
                'uploads' => $this->innerComponentUploadsConfiguration(),
                'embeds' => $this->innerComponentEmbedsConfiguration(),
            ],
        );
    }
}
