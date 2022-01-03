<?php

namespace Code16\Sharp\Auth;

class SharpEntityPolicyLegacyDecorator extends SharpEntityPolicy
{
    protected $legacyPolicy;
    protected bool $isDashboard;

    public function __construct($legacyPolicy, bool $isDashboard)
    {
        $this->legacyPolicy = $legacyPolicy;
        $this->isDashboard = $isDashboard;
    }

    public function entity($user): bool
    {
        return $this->callFromDecorated("entity", [$user]);
    }

    private function callFromDecorated(string $method, array $params): bool
    {
        if($this->isDashboard && $method === "view") {
            $method = "entity"; // Avoid a BC because function was renamed in Sharp 7
        }
        return method_exists($this->legacyPolicy, $method)
            ? call_user_func_array([$this->legacyPolicy, $method], $params)
            : true;
    }

    public function view($user, $instanceId): bool
    {
        return $this->callFromDecorated("view", [$user, $instanceId]);
    }

    public function update($user, $instanceId): bool
    {
        return $this->callFromDecorated("update", [$user, $instanceId]);
    }

    public function create($user): bool
    {
        return $this->callFromDecorated("create", [$user]);
    }

    public function delete($user, $instanceId)
    {
        return $this->callFromDecorated("delete", [$user, $instanceId]);
    }
}