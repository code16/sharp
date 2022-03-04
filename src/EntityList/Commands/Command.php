<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\Form\HandleFormFields;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Contracts\Validation\Factory as Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Base class for Commands. Handle returns (info, refresh, reload...),
 * form creation, and validation.
 */
abstract class Command
{
    use HandleFormFields;
    use WithCustomTransformers;

    protected int $groupIndex = 0;

    protected function info(string $message): array
    {
        return [
            'action'  => 'info',
            'message' => $message,
        ];
    }

    protected function link(string $link): array
    {
        return [
            'action' => 'link',
            'link'   => $link,
        ];
    }

    protected function reload(): array
    {
        return [
            'action' => 'reload',
        ];
    }

    protected function refresh($ids): array
    {
        return [
            'action' => 'refresh',
            'items'  => (array) $ids,
        ];
    }

    protected function view(string $bladeView, array $params = []): array
    {
        return [
            'action' => 'view',
            'html'   => view($bladeView, $params)->render(),
        ];
    }

    protected function download(string $filePath, string $fileName = null, string $diskName = null): array
    {
        return [
            'action' => 'download',
            'file'   => $filePath,
            'disk'   => $diskName,
            'name'   => $fileName,
        ];
    }

    /**
     * Check if the current user is allowed to use this Command.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array|bool
     */
    public function getGlobalAuthorization()
    {
        return $this->authorize();
    }

    public function confirmationText(): ?string
    {
        return null;
    }

    /**
     * Build the optional Command form, calling ->addField().
     */
    public function buildFormFields(): void
    {
    }

    /**
     * Build the optional Command form layout.
     */
    public function buildFormLayout(FormLayoutColumn &$column): void
    {
    }

    public function form(): array
    {
        return $this->fields();
    }

    public function formLayout(): ?array
    {
        if (!$this->fields) {
            return null;
        }

        $column = new FormLayoutColumn(12);
        $this->buildFormLayout($column);

        if (empty($column->fieldsToArray()['fields'])) {
            foreach ($this->fields as $field) {
                $column->withSingleField($field->key());
            }
        }

        return $column->fieldsToArray()['fields'];
    }

    public function setGroupIndex($index): void
    {
        $this->groupIndex = $index;
    }

    public function groupIndex(): int
    {
        return $this->groupIndex;
    }

    public function validate(array $params, array $rules, array $messages = []): void
    {
        $validator = app(Validator::class)->make($params, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException(
                $validator,
                new JsonResponse($validator->errors()->getMessages(), 422)
            );
        }
    }

    public function description(): string
    {
        return '';
    }

    public function formModalTitle(): string
    {
        return '';
    }

    abstract public function label(): ?string;
}
