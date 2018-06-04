<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class PilotDownloadResume extends InstanceCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Download resume";
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     */
    public function execute($instanceId, array $data = []): array
    {
        return $this->download("pdf/pilot-resume.pdf", "local");
    }
}
