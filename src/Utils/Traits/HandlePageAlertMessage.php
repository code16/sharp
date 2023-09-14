<?php

namespace Code16\Sharp\Utils\Traits;

use Code16\Sharp\Utils\PageAlerts\PageAlert;

trait HandlePageAlertMessage
{
    protected PageAlert $pageAlert;

    public final function pageAlert(?array $data = []): ?array
    {
        $this->pageAlert = new PageAlert();
        $this->pageAlert->setData($data ?: []);
        $this->buildPageAlert($this->pageAlert);

        return $this->pageAlert->toArray();
    }

    protected function buildPageAlert(PageAlert $pageAlert): void
    {
    }
}
