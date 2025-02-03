<?php

namespace Code16\Sharp\Http\Requests;

use Illuminate\Http\Request;

/**
 * @internal Override the request to remove "flash" QS parameters.
 */
class SharpInertiaRequest extends Request
{
    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): void
    {
        parent::initialize($query, $request, $attributes, $cookies, $files, $server, $content);
        
        $this->cleanFlashQueryParameters();
    }
    
    protected function cleanFlashQueryParameters(): void
    {
        $this->query->remove('highlighted_entity_key');
        $this->query->remove('highlighted_instance_id');
        $this->query->remove('popstate');
        
        $this->server->set('QUERY_STRING', static::normalizeQueryString(http_build_query($this->query->all(), '', '&')));
    }
}
