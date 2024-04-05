<?php

namespace Code16\Sharp\Config;

use Code16\Sharp\Auth\Impersonate\SharpDefaultEloquentImpersonationHandler;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Code16\Sharp\Utils\Menu\SharpMenu;
use Illuminate\Support\Traits\Conditionable;

class SharpConfigBuilder
{
    use Conditionable;

    protected array $config = [
        'name' => 'Sharp',
        'custom_url_segment' => 'sharp',
        'display_sharp_version_in_title' => true,
        'display_breadcrumb' => true,
        'entities' => [],
        'entity_resolver' => null,
        'global_filters' => [],
        'middleware' => [
            'common' => [
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],
            'web' => [
                \Code16\Sharp\Http\Middleware\InvalidateCache::class,
                \Code16\Sharp\Http\Middleware\HandleSharpErrors::class,
                \Code16\Sharp\Http\Middleware\HandleInertiaRequests::class,
            ],
            'api' => [
                \Code16\Sharp\Http\Middleware\Api\BindSharpValidationResolver::class,
                \Code16\Sharp\Http\Middleware\Api\HandleSharpApiErrors::class,
            ],
        ],
        'auth' => [
            'impersonate' => [
                'enabled' => false,
            ],
        ],
        'uploads' => [
            'tmp_disk' => 'local',
            'tmp_dir' => 'tmp',
            'thumbnails_disk' => 'public',
            'thumbnails_dir' => 'thumbnails',
            'transform_keep_original_image' => true,
            'max_file_size' => 5,
            'model_class' => null,
            'image_driver' => \Intervention\Image\Drivers\Gd\Driver::class,
            'file_handling_queue' => 'default',
            'file_handling_queue_connection' => 'sync',
        ],
        'search' => [
            'enabled' => false,
        ],
        'theme' => [
            'primary_color' => '#004c9b',
            'favicon_url' => null,
            'logo_url' => null,
            'logo_height' => '1.5rem',
        ],
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

    public function displaySharpVersionInTitle(bool $displaySharpVersionInTitle = true): self
    {
        $this->config['display_sharp_version_in_title'] = $displaySharpVersionInTitle;

        return $this;
    }

    public function displayBreadcrumb(bool $displayBreadcrumb = true): self
    {
        $this->config['display_breadcrumb'] = $displayBreadcrumb;

        return $this;
    }

    public function addEntity(string $key, string $entityClass): self
    {
        $this->config['entities'][$key] = $entityClass;
        $this->config['entity_resolver'] = null;

        return $this;
    }

    public function declareEntityResolver(string $resolverClassName): self
    {
        $this->config['entity_resolver'] = $resolverClassName;
        $this->config['entities'] = [];

        return $this;
    }

    public function addGlobalFilter(string|GlobalRequiredFilter $filter): self
    {
        $this->config['global_filters'][] = instanciate($filter);

        return $this;
    }

    public function setLeftMenu(string|SharpMenu $sharpMenu): self
    {
        $this->config['menu'] = instanciate($sharpMenu);

        return $this;
    }

    public function appendToMiddlewareWebGroup(string $middlewareClassName): self
    {
        $this->config['middleware']['web'][] = $middlewareClassName;

        return $this;
    }

    public function appendToMiddlewareApiGroup(string $middlewareClassName): self
    {
        $this->config['middleware']['api'][] = $middlewareClassName;

        return $this;
    }

    public function appendToMiddleware(string $middlewareClassName): self
    {
        $this->config['middleware']['common'][] = $middlewareClassName;

        return $this;
    }

    public function replaceAllMiddleware(array $middlewareList): self
    {
        $this->config['middleware'] = $middlewareList;

        return $this;
    }

    public function enableGlobalSearch(SharpSearchEngine|string $engine, string $placeholder = null): self
    {
        $this->config['search'] = [
            'enabled' => true,
            'placeholder' => $placeholder,
            'engine' => instanciate($engine),
        ];

        return $this;
    }

    public function disableGlobalSearch(): self
    {
        $this->config['search'] = [
            'enabled' => false,
        ];

        return $this;
    }

    public function configureUploads(
        string $uploadDisk = 'local',
        string $uploadDirectory = 'tmp',
        int    $globalMaxFileSize = 5,
        string $fileHandingQueue = 'default',
        string $fileHandlingQueueConnection = 'sync',
    ): self
    {
        $this->config['uploads']['tmp_disk'] = $uploadDisk;
        $this->config['uploads']['tmp_dir'] = $uploadDirectory;
        $this->config['uploads']['max_file_size'] = $globalMaxFileSize;
        $this->config['uploads']['file_handling_queue'] = $fileHandingQueue;
        $this->config['uploads']['file_handling_queue_connection'] = $fileHandlingQueueConnection;

        return $this;
    }

    public function configureUploadsThumbnailCreation(
        string $thumbnailsDisk = 'public',
        string $thumbnailsDir = 'thumbnails',
        bool $keepOriginalImageOnTransform = true,
        ?string $uploadModelClass = null,
        string $imageDriverClass = \Intervention\Image\Drivers\Gd\Driver::class,
    ): self
    {
        $this->config['uploads']['thumbnails_disk'] = $thumbnailsDisk;
        $this->config['uploads']['thumbnails_dir'] = $thumbnailsDir;
        $this->config['uploads']['transform_keep_original_image'] = $keepOriginalImageOnTransform;
        $this->config['uploads']['model_class'] = $uploadModelClass;
        $this->config['uploads']['image_driver'] = $imageDriverClass;

        return $this;
    }

    public function setThemeColor(string $hexColor): self
    {
        $this->config['theme']['primary_color'] = $hexColor;

        return $this;
    }

    public function setThemeLogo(string $logoUrl, string $logoHeight = '1.5rem', ?string $faviconUrl = null): self
    {
        $this->config['theme']['logo_url'] = $logoUrl;
        $this->config['theme']['logo_height'] = $logoHeight;
        $this->config['theme']['favicon_url'] = $faviconUrl;

        return $this;
    }

    public function enableImpersonation(SharpImpersonationHandler|string $handler = null): self
    {
        $this->config['auth']['impersonate'] = [
            'enabled' => true,
            'handler' => $handler
                ? instanciate($handler)
                : app(SharpDefaultEloquentImpersonationHandler::class),
        ];

        return $this;
    }

    public function disableImpersonation(): self
    {
        $this->config['auth']['impersonate'] = [
            'enabled' => false,
        ];

        return $this;
    }

    public function get(string $key): mixed
    {
        if (str($key)->contains('.')) {
            $parts = explode('.', $key);
            $config = $this->config;

            foreach ($parts as $part) {
                if (! isset($config[$part])) {
                    return null;
                }

                $config = $config[$part];
            }

            return $config;
        }

        return $this->config[$key] ?? null;
    }
}