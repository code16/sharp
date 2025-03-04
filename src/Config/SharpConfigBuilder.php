<?php

namespace Code16\Sharp\Config;

use Closure;
use Code16\Sharp\Auth\Impersonate\SharpDefaultEloquentImpersonationHandler;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;
use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityResolver;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Code16\Sharp\Utils\Menu\SharpMenu;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Traits\Conditionable;
use Spatie\StructureDiscoverer\Discover;

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
                \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
                \Code16\Sharp\Http\Middleware\AddLinkHeadersForPreloadedRequests::class,
            ],
            'api' => [
                \Code16\Sharp\Http\Middleware\Api\HandleSharpApiErrors::class,
            ],
        ],
        'auth' => [
            'login_page_url' => null,
            'display_attribute' => 'name',
            'login_attribute' => 'email',
            'password_attribute' => 'password',
            'impersonate' => [
                'enabled' => false,
            ],
            'forgotten_password' => [
                'enabled' => false,
            ],
            'rate_limiting' => [
                'enabled' => true,
                'max_attempts' => 5,
            ],
            '2fa' => [
                'enabled' => false,
            ],
            'suggest_remember_me' => false,
            'login_form_message' => null,
            'guard' => null,
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
        'assets' => [],
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

    public function declareEntityResolver(SharpEntityResolver|string $resolver): self
    {
        $resolver = instanciate($resolver);

        if (! $resolver instanceof SharpEntityResolver) {
            throw new SharpInvalidEntityKeyException('Invalid entity resolver class: it should extend '.SharpEntityResolver::class.'.');
        }

        $this->config['entity_resolver'] = instanciate($resolver);
        $this->config['entities'] = [];

        return $this;
    }

    public function autodiscoverEntities(string $path = 'Sharp/Entities'): self
    {
        $entityClasses = Discover::in(app_path($path))
            ->extending(SharpEntity::class, SharpDashboardEntity::class)
            ->get();

        if (empty($entityClasses)) {
            throw new SharpInvalidConfigException('Autodiscover failed: no entities found in the given path.');
        }

        collect($entityClasses)
            ->each(fn (string $entityClass) => $this
                ->addEntity(
                    str(class_basename($entityClass))
                        ->before('Entity')
                        ->kebab()
                        ->plural()
                        ->toString(),
                    $entityClass
                )
            );

        return $this;
    }

    public function addGlobalFilter(string|GlobalRequiredFilter $filter): self
    {
        $this->config['global_filters'][] = instanciate($filter);

        return $this;
    }

    public function setSharpMenu(string|SharpMenu $sharpMenu): self
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

    public function enableGlobalSearch(SharpSearchEngine|string $engine, ?string $placeholder = null): self
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
        int $globalMaxFileSize = 5,
        bool $keepOriginalImageOnTransform = true,
        string $fileHandingQueue = 'default',
        string $fileHandlingQueueConnection = 'sync',
    ): self {
        $this->config['uploads']['tmp_disk'] = $uploadDisk;
        $this->config['uploads']['tmp_dir'] = $uploadDirectory;
        $this->config['uploads']['max_file_size'] = $globalMaxFileSize;
        $this->config['uploads']['transform_keep_original_image'] = $keepOriginalImageOnTransform;
        $this->config['uploads']['file_handling_queue'] = $fileHandingQueue;
        $this->config['uploads']['file_handling_queue_connection'] = $fileHandlingQueueConnection;

        return $this;
    }

    public function configureUploadsThumbnailCreation(
        string $thumbnailsDisk = 'public',
        string $thumbnailsDir = 'thumbnails',
        ?string $uploadModelClass = null,
        string $imageDriverClass = \Intervention\Image\Drivers\Gd\Driver::class,
    ): self {
        $this->config['uploads']['thumbnails_disk'] = $thumbnailsDisk;
        $this->config['uploads']['thumbnails_dir'] = $thumbnailsDir;
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

    public function enableImpersonation(SharpImpersonationHandler|Closure|string|null $handler = null): self
    {
        if ($handler instanceof Closure) {
            $handler = new class($handler) extends SharpImpersonationHandler
            {
                public function __construct(private readonly Closure $callback) {}

                public function getUsers(): array
                {
                    return call_user_func($this->callback);
                }
            };
        }

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

    public function enableForgottenPassword(
        PasswordBroker|string|null $broker = null,
        ?Closure $resetCallback = null,
        bool $showResetLinkInLoginForm = true,
    ): self {
        $this->config['auth']['forgotten_password'] = [
            'enabled' => true,
            'password_broker' => $broker ? instanciate($broker) : null,
            'reset_password_callback' => $resetCallback,
            'show_reset_link_in_login_form' => $showResetLinkInLoginForm,
        ];

        return $this;
    }

    public function disableForgottenPassword(): self
    {
        $this->config['auth']['forgotten_password'] = [
            'enabled' => false,
        ];

        return $this;
    }

    public function enableLoginRateLimiting(?int $maxAttempts = 5): self
    {
        $this->config['auth']['rate_limiting'] = [
            'enabled' => true,
            'max_attempts' => $maxAttempts,
        ];

        return $this;
    }

    public function disableLoginRateLimiting(): self
    {
        $this->config['auth']['rate_limiting'] = [
            'enabled' => false,
        ];

        return $this;
    }

    public function setLoginAttributes(string $login = 'email', string $password = 'password'): self
    {
        $this->config['auth']['login_attribute'] = $login;
        $this->config['auth']['password_attribute'] = $password;

        return $this;
    }

    public function setUserDisplayAttribute(string $displayAttribute): self
    {
        $this->config['auth']['display_attribute'] = $displayAttribute;

        return $this;
    }

    public function setAuthCustomGuard(?string $guardName): self
    {
        $this->config['auth']['guard'] = $guardName;

        return $this;
    }

    public function enable2faByNotification(): self
    {
        $this->config['auth']['2fa'] = [
            'enabled' => true,
            'handler' => 'notification',
        ];

        return $this;
    }

    public function enable2faByTotp(): self
    {
        $this->config['auth']['2fa'] = [
            'enabled' => true,
            'handler' => 'totp',
        ];

        return $this;
    }

    public function enable2faCustom(string|Sharp2faHandler $customHandler): self
    {
        $this->config['auth']['2fa'] = [
            'enabled' => true,
            'handler' => instanciate($customHandler),
        ];

        return $this;
    }

    public function disable2fa(): self
    {
        $this->config['auth']['2fa'] = [
            'enabled' => false,
        ];

        return $this;
    }

    public function suggestRememberMeOnLoginForm(bool $suggestRememberMe = true): self
    {
        $this->config['auth']['suggest_remember_me'] = $suggestRememberMe;

        return $this;
    }

    public function appendMessageOnLoginForm(string|View $message): self
    {
        $this->config['auth']['login_form_message'] = $message;

        return $this;
    }

    public function redirectLoginToUrl(?string $url): self
    {
        $this->config['auth']['login_page_url'] = $url;

        return $this;
    }

    public function loadViteAssets(array|Vite $assets): self
    {
        $this->config['assets'][] = $assets instanceof Vite
            ? $assets->toHtml()
            : app(Vite::class)->__invoke($assets)->toHtml();

        return $this;
    }

    public function loadStaticCss(string $url): self
    {
        $this->config['assets'][] = sprintf('<link rel="stylesheet" href="%s">', $url);

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
