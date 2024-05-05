<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\Enums\CommandAction;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\HasModalFormLayout;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFormFields;
use Code16\Sharp\Utils\SharpNotification;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Traits\HandleValidation;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class Command
{
    use HandleFormFields;
    use HandlePageAlertMessage;
    use WithCustomTransformers;
    use HandleValidation;
    use HasModalFormLayout;

    protected int $groupIndex = 0;
    protected ?string $commandKey = null;
    private ?string $formModalTitle = null;
    private ?string $formModalButtonLabel = null;
    private ?string $confirmationTitle = null;
    private ?string $confirmationDescription = null;
    private ?string $description = null;

    protected function info(string $message): array
    {
        return [
            'action' => CommandAction::Info->value,
            'message' => $message,
        ];
    }

    protected function link(string $link): array
    {
        return [
            'action' => CommandAction::Link->value,
            'link' => $link,
        ];
    }

    protected function reload(): array
    {
        return [
            'action' => CommandAction::Reload->value,
        ];
    }

    protected function refresh($ids): array
    {
        return [
            'action' => CommandAction::Refresh->value,
            'items' => (array) $ids,
        ];
    }

    protected function view(string $bladeView, array $params = []): array
    {
        return [
            'action' => CommandAction::View->value,
            'html' => view($bladeView, $params)->render(),
        ];
    }

    protected function download(string $filePath, string $fileName = null, string $diskName = null): array
    {
        return [
            'action' => CommandAction::Download->value,
            'file' => $filePath,
            'disk' => $diskName,
            'name' => $fileName,
        ];
    }

    protected function streamDownload(string $fileContent, string $fileName): array
    {
        return [
            'action' => CommandAction::StreamDownload->value,
            'content' => $fileContent,
            'name' => $fileName,
        ];
    }

    public function notify(string $title): SharpNotification
    {
        return new SharpNotification($title);
    }

    final protected function configureFormModalTitle(string $formModalTitle): self
    {
        $this->formModalTitle = $formModalTitle;

        return $this;
    }

    final protected function configureFormModalButtonLabel(string $formModalButtonLabel): self
    {
        $this->formModalButtonLabel = $formModalButtonLabel;

        return $this;
    }

    final protected function configureDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    final protected function configureConfirmationText(string $confirmationText): self
    {
        $this->confirmationTitle = strlen($confirmationText) > 50 ? __('sharp::modals.confirm.title') : $confirmationText;
        $this->confirmationDescription = strlen($confirmationText) > 50 ? $confirmationText : null;

        return $this;
    }

    /**
     * Check if the current user is allowed to use this Command.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getGlobalAuthorization(): array|bool
    {
        return $this->authorize();
    }

    final public function getConfirmationTitle(): ?string
    {
        return $this->confirmationTitle;
    }
    
    final public function getConfirmationDescription(): ?string
    {
        return $this->confirmationDescription;
    }

    final public function getDescription(): ?string
    {
        return $this->description;
    }

    final public function getFormModalTitle(): ?string
    {
        return $this->formModalTitle;
    }

    final public function getFormModalButtonLabel(): ?string
    {
        return $this->formModalButtonLabel;
    }

    /**
     * Build the optional Command config with configure... methods.
     */
    public function buildCommandConfig(): void
    {
    }

    /**
     * Build the optional Command form.
     */
    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    /**
     * Build the optional Command form layout.
     */
    public function buildFormLayout(FormLayoutColumn &$column): void
    {
    }

    final public function form(): array
    {
        return $this->fields();
    }

    final public function formLayout(): ?array
    {
        return $this->modalFormLayout(function (FormLayoutColumn $column) {
            $this->buildFormLayout($column);
        });
    }

    final public function setGroupIndex($index): void
    {
        $this->groupIndex = $index;
    }

    final public function setCommandKey(string $key): void
    {
        $this->commandKey = $key;
    }

    final public function groupIndex(): int
    {
        return $this->groupIndex;
    }

    final public function getCommandKey(): string
    {
        return $this->commandKey ?? class_basename($this::class);
    }

    public function getDataLocalizations(): array
    {
        return [];
    }

    abstract public function label(): ?string;
}
