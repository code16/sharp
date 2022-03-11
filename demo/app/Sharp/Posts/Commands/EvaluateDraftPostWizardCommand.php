<?php

namespace App\Sharp\Posts\Commands;

use App\Models\Post;
use Code16\Sharp\EntityList\Commands\Wizards\InstanceWizardCommand;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class EvaluateDraftPostWizardCommand extends InstanceWizardCommand
{
    public function label(): ?string
    {
        return 'Evaluate this draft post...';
    }

    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormSelectField::make('decision', ['yes' => 'Accept draft', 'no' => 'Reject draft'])
                ->setLabel('Decision')
                ->setHelpMessage('If you choose to reject, you’ll have the opportunity to say why in the next step. This could have been done with a simple conditional display.'),
        );
    }

    public function executeFirstStep(mixed $instanceId, array $data = []): array
    {
        $this->validate($data, [
            'decision' => ['required'],
        ]);

        if ($data['decision'] === 'yes') {
            Post::find($instanceId)->update(['state' => 'online']);

            return $this->reload();
        }

        $this->getWizardContext()->put('post_title', Post::find($instanceId)->getTranslation('title', 'en'));

        return $this->toStep('refusal_reason');
    }

    protected function initialDataForStepRefusalReason(mixed $instanceId): array
    {
        $this->getWizardContext()->validate(['post_title' => ['required']]);

        return [
            'reason' => sprintf(
                "I choose to refuse your “%s” post because:\n\n",
                $this->getWizardContext()->get('post_title'),
            ),
        ];
    }

    public function buildFormFieldsForStepRefusalReason(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextareaField::make('reason')
                    ->setLabel('Refusal reason')
                    ->setRowCount(8),
            );
    }

    public function executeStepRefusalReason(mixed $instanceId, array $data = []): array
    {
        $this->validate($data, [
            'reason' => ['required'],
        ]);

        return $this->info('Message sent.');
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        return auth()->user()->isAdmin() && ! Post::find($instanceId)->isOnline();
    }
}
