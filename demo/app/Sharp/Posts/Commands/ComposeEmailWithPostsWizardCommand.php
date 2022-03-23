<?php

namespace App\Sharp\Posts\Commands;

use App\Models\Post;
use App\Models\User;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

class ComposeEmailWithPostsWizardCommand extends EntityWizardCommand
{
    public function label(): ?string
    {
        return 'Compose an email with chosen posts...';
    }

    public function buildCommandConfig(): void
    {
        $this->configureDescription('Use this wizard command to compose a message with chosen posts links');
    }

    public function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
    {
        $posts = Post::online()
            ->publishedSince(today()->subDays(30))
            ->orderBy('published_at', 'desc')
            ->get()
            ->mapWithKeys(fn (Post $post) => [$post->id => $post->getTranslation('title', 'en')]);

        $formFields->addField(
            SharpFormSelectField::make('posts', $posts->toArray())
                ->setMultiple()
                ->setLabel('Posts to add to the message')
                ->setHelpMessage('Only posts published in the last 30 days'),
        );
    }

    public function executeFirstStep(array $data = []): array
    {
        $this->validate($data, [
            'posts' => ['required', 'array'],
        ]);

        $this->getWizardContext()->put('posts', $data['posts']);

        return $this->toStep('compose_message');
    }

    protected function initialDataForStepComposeMessage(): array
    {
        $this->getWizardContext()->validate(['posts' => ['required', 'array']]);

        return [
            'message' => collect(
                [
                    'Here’s a list of posts I think you may like:',
                ])
                ->merge(
                    Post::whereIn('id', $this->getWizardContext()->get('posts'))
                        ->get()
                        ->map(fn (Post $post) => sprintf(
                            ' - %s (%s)',
                            $post->getTranslation('title', 'en'),
                            url("post/{$post->id}"),
                        )),
                )
                ->implode("\n"),
        ];
    }

    public function buildFormFieldsForStepComposeMessage(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextareaField::make('message')
                    ->setLabel('Message text')
                    ->setRowCount(8),
            )
            ->addField(
                SharpFormCheckField::make('test', 'Send me a test email')
                    ->setHelpMessage('If checked, the command will stop here; if not, there is one final step'),
            );
    }

    public function executeStepComposeMessage(array $data = []): array
    {
        $this->validate($data, [
            'message' => ['required'],
        ]);

        $this->getWizardContext()->put('message', $data['message']);

        if ($data['test'] ?? false) {
            return $this->info('Message sent to you!');
        }

        return $this->toStep('choose_recipients');
    }

    protected function initialDataForStepChooseRecipients(): array
    {
        $this->getWizardContext()->validate([
            'posts' => ['required', 'array'],
            'message' => ['required'],
        ]);

        return [
            'message' => [
                'text' => Str::markdown($this->getWizardContext()->get('message')),
            ],
        ];
    }

    public function buildFormFieldsForStepChooseRecipients(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormHtmlField::make('message')
                    ->setInlineTemplate('<div style="max-height: 100px; overflow: auto; border: 1px solid #ddd; padding: 10px 15px; font-size: .85em;" v-html="text"></div>')
                    ->setLabel('Message'),
            )
            ->addField(
                SharpFormSelectField::make('recipients', User::pluck('name', 'id')->toArray())
                    ->setLabel('Recipients')
                    ->setMultiple(),
            );
    }

    public function executeStepChooseRecipients(array $data = []): array
    {
        $this->validate($data, [
            'recipients' => ['required', 'array'],
        ]);

        return $this->info('Message sent to all of them!');
    }
}
