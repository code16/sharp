<?php

namespace Code16\Sharp\Utils\Links;

class LinkToForm extends LinkToShowPage
{
    protected bool $throughShowPage = false;

    public function throughShowPage($throughShowPage = true): self
    {
        $this->throughShowPage = $throughShowPage;

        return $this;
    }

    protected function generateUrl(): string
    {
        if (isset($this->breadcrumbBuilder)) {
            return parent::generateUrl();
        }

        if($this->instanceId) {
            return route('code16.sharp.form.edit', [
                'uri' => sprintf(
                    's-list/%s%s',
                    $this->entityKey,
                    $this->throughShowPage ? '/' . parent::generateUri() : ''
                ),
                'entityKey' => $this->entityKey,
                'instanceId' => $this->instanceId,
            ]);
        }

        return route('code16.sharp.form.create', [
            'uri' => sprintf('s-list/%s', $this->entityKey),
            'entityKey' => $this->entityKey,
        ]);
    }

    protected function generateUri(): string
    {
        $uri = sprintf('s-form/%s/%s', $this->entityKey, $this->instanceId);

        if ($this->throughShowPage) {
            $uri = sprintf('%s/%s', parent::generateUri(), $uri);
        }

        return $uri;
    }
}
