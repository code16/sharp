<?php

namespace Code16\Sharp\Config;

class SharpConfigBuilder
{
    protected array $config = [
        'name' => 'Sharp',
        'custom_url_segment' => 'sharp',
        'display_sharp_version_in_title' => true,
        'display_breadcrumb' => true,
    ];

    public function setName(string $name): self
    {
        $this->config['name'] = $name;

        return $this;
    }

    public function setCustomUrlSegment(string $customUrlSegment): self
    {
        $this->config['custom_url_segment'] = $customUrlSegment;

        return $this;
    }

    public function setDisplaySharpVersionInTitle(bool $displaySharpVersionInTitle = true): self
    {
        $this->config['display_sharp_version_in_title'] = $displaySharpVersionInTitle;

        return $this;
    }

    public function setDisplayBreadcrumb(bool $displayBreadcrumb = true): self
    {
        $this->config['display_breadcrumb'] = $displayBreadcrumb;

        return $this;
    }

    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }
}