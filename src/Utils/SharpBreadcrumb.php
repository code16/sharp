<?php

namespace Code16\Sharp\Utils;

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Http\Context\Util\BreadcrumbItem;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SharpBreadcrumb
{
    private ?string $currentInstanceLabel = null;

    public function __construct(protected CurrentSharpRequest $currentSharpRequest)
    {
    }
    
    final public function setCurrentInstanceLabel(?string $label): self
    {
        $this->currentInstanceLabel = $label;
        
        return $this;
    }

    public function getItems(): array
    {
        $url = sharp()->config()->get('custom_url_segment');
        $breadcrumb = $this->currentSharpRequest->breadcrumb();

        return $breadcrumb
            ->map(function ($item, $index) use (&$url, $breadcrumb) {
                $url = sprintf('%s/%s/%s',
                    $url,
                    $item->type,
                    isset($item->instance) ? "{$item->key}/{$item->instance}" : $item->key,
                );
                $isLeaf = $index === sizeof($breadcrumb) - 1;

                return [
                    'type' => $this->getFrontTypeNameFor($item->type),
                    'label' => sharp()->config()->get('display_breadcrumb')
                        ? $this->getBreadcrumbLabelFor($item, $isLeaf)
                        : '',
                    'documentTitleLabel' => $this->getDocumentTitleLabelFor($item, $isLeaf),
                    'entityKey' => $item->key,
                    'url' => url($url),
                ];
            })
            ->all();
    }

    private function getFrontTypeNameFor(string $type): string
    {
        return match ($type) {
            's-list' => 'list',
            's-form' => 'form',
            's-show' => 'show',
            's-dashboard' => 'dashboard',
            default => '',
        };
    }

    private function getBreadcrumbLabelFor(BreadcrumbItem $item, bool $isLeaf): string
    {
        switch ($item->type) {
            case 's-list':
                return currentSharpRequest()->getEntityMenuLabel($item->key) ?: trans('sharp::breadcrumb.entityList');
            case 's-dashboard':
                return currentSharpRequest()->getEntityMenuLabel($item->key) ?: trans('sharp::breadcrumb.dashboard');
            case 's-show':
                return trans('sharp::breadcrumb.show', [
                    'entity' => $this->getEntityLabelForInstance($item, $isLeaf),
                ]);
            case 's-form':
                // A Form is always a leaf
                $previousItem = $this->currentSharpRequest->breadcrumb()[$item->depth - 1];

                if ($previousItem->type === 's-show' && ! $this->isSameEntityKeys($previousItem->key, $item->key, true)) {
                    // The form entityKey is different from the previous entityKey in the breadcrumb: we are in a EEL case.
                    return isset($item->instance)
                        ? trans('sharp::breadcrumb.form.edit_entity', [
                            'entity' => $this->getEntityLabelForInstance($item, true),
                        ])
                        : trans('sharp::breadcrumb.form.create_entity', [
                            'entity' => $this->getEntityLabelForInstance($item, true),
                        ]);
                }

                return isset($item->instance) || ($previousItem->type === 's-show' && ! isset($previousItem->instance))
                    ? trans('sharp::breadcrumb.form.edit')
                    : trans('sharp::breadcrumb.form.create');
        }

        return $item->key;
    }

    /**
     * Return first part of document title when needed :
     * {documentTitleLabel}, {entityLabel} | {site}.
     */
    private function getDocumentTitleLabelFor(BreadcrumbItem $item, bool $isLeaf): ?string
    {
        if (! $isLeaf) {
            return null;
        }
        
        $previousItem = $this->currentSharpRequest->breadcrumb()[$item->depth - 1] ?? null;
        
        return match ($item->type) {
            's-show' => trans('sharp::breadcrumb.show', [
                'entity' => $this->getEntityLabelForInstance($item, $isLeaf),
            ]),
            's-form' => isset($item->instance) || ($previousItem->type === 's-show' && ! isset($previousItem->instance))
                ? trans('sharp::breadcrumb.form.edit_entity', [
                    'entity' => $this->getEntityLabelForInstance($item, $isLeaf),
                ])
                : trans('sharp::breadcrumb.form.create_entity', [
                    'entity' => $this->getEntityLabelForInstance($item, $isLeaf),
                ]),
            default => null
        };
    }

    /**
     * Only for Shows and Forms.
     */
    private function getEntityLabelForInstance(BreadcrumbItem $item, bool $isLeaf): string
    {
        $cacheKey = "sharp.breadcrumb.{$item->key}.{$item->type}.{$item->instance}";
        
        if ($item->isForm() && ($cached = Cache::get("sharp.breadcrumb.{$item->key}.s-show.{$item->instance}"))) {
            return $cached;
        }

        if ($isLeaf && $this->currentInstanceLabel) {
            Cache::put($cacheKey, $this->currentInstanceLabel, now()->addMinutes(30));

            return $this->currentInstanceLabel;
        }

        if (! $isLeaf) {
            // The breadcrumb custom label may have been cached on the way up
            if ($value = Cache::get($cacheKey)) {
                return $value;
            }
        }

        $entity = app(SharpEntityManager::class)->entityFor($item->key);

        if (str_contains($item->key, ':')) {
            return $entity->getMultiforms()[Str::after($item->key, ':')][1] ?? $entity->getLabel();
        }

        return $entity->getLabel();
    }

    private function isSameEntityKeys(string $key1, string $key2, bool $compareBaseEntities): bool
    {
        if ($compareBaseEntities) {
            $key1 = explode(':', $key1)[0];
            $key2 = explode(':', $key2)[0];
        }

        return $key1 === $key2;
    }
}
