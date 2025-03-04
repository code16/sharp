<?php

namespace Code16\Sharp\Utils\Links;

use Closure;

class LinkToShowPage extends SharpLinkTo
{
    protected string $instanceId;
    protected BreadcrumbBuilder $breadcrumbBuilder;

    public static function make(string $entityClassOrKey, string $instanceId): self
    {
        $instance = new static($entityClassOrKey);
        $instance->instanceId = $instanceId;

        return $instance;
    }

    /**
     * @param  (\Closure(BreadcrumbBuilder): BreadcrumbBuilder)  $closure
     * @return $this
     */
    public function withBreadcrumb(Closure $closure): self
    {
        $this->breadcrumbBuilder = $closure(new BreadcrumbBuilder());

        return $this;
    }

    public function renderAsUrl(): string
    {
        if (isset($this->breadcrumbBuilder)) {
            return url(
                sprintf(
                    '%s/%s/%s',
                    sharp()->config()->get('custom_url_segment'),
                    $this->breadcrumbBuilder->generateUri(),
                    $this->generateUri()
                )
            );
        }

        return $this->generateUrl();
    }

    protected function generateUrl(): string
    {
        return route('code16.sharp.show.show', [
            'parentUri' => sprintf('s-list/%s', $this->entityKey),
            'entityKey' => $this->entityKey,
            'instanceId' => $this->instanceId,
        ]);
    }

    protected function generateUri(): string
    {
        return sprintf('s-show/%s/%s', $this->entityKey, $this->instanceId);
    }
}
