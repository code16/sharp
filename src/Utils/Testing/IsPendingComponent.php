<?php

namespace Code16\Sharp\Utils\Testing;

use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Links\BreadcrumbBuilder;
use Code16\Sharp\Utils\Testing\EntityList\PendingEntityList;
use Code16\Sharp\Utils\Testing\Form\PendingForm;
use Code16\Sharp\Utils\Testing\Show\PendingShow;

trait IsPendingComponent
{
    use GeneratesCurrentPageUrl;

    protected function getParentUri(): string
    {
        return $this->breadcrumbBuilder($this->parents())->generateUri();
    }

    protected function getCurrentUri(): string
    {
        return $this->breadcrumbBuilder([...$this->parents(), $this])->generateUri();
    }

    protected function getCurrentPageUrlFromParents(): string
    {
        return $this->buildCurrentPageUrl($this->getCurrentUri());
    }

    protected function breadcrumbBuilder(array $components): BreadcrumbBuilder
    {
        $breadcrumb = new BreadcrumbBuilder();
        $first = $components[0] ?? $this;

        // fill the breadcrumb if needed
        if ($first instanceof PendingShow && $first->instanceId) {
            $breadcrumb->appendEntityList($first->entityKey);
        } elseif ($first instanceof PendingForm) {
            if ($first->form instanceof SharpSingleForm) {
                $breadcrumb->appendSingleShowPage($first->entityKey);
            } else {
                $breadcrumb->appendEntityList($first->entityKey);
                if (app(SharpEntityManager::class)->entityFor($first->entityKey)->hasShow() && $first->instanceId) {
                    $breadcrumb->appendShowPage($first->entityKey, $first->instanceId);
                }
            }
        }

        foreach ($components as $component) {
            if ($component instanceof PendingEntityList && ! $component->parent) {
                $breadcrumb->appendEntityList($component->entityKey);
            } elseif ($component instanceof PendingShow) {
                if ($component->instanceId) {
                    $breadcrumb->appendShowPage($component->entityKey, $component->instanceId);
                } else {
                    $breadcrumb->appendSingleShowPage($component->entityKey);
                }
            }
        }

        return $breadcrumb;
    }

    protected function parents(): array
    {
        $parents = [];

        for ($parent = $this->parent; $parent; $parent = $parent->parent) {
            $parents[] = $parent;
        }

        return array_reverse($parents);
    }
}
