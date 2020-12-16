<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Form\SharpSingleForm;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * This middleware is responsible for storing the current breadcrumb
 * (ie: navigation path) in session.
 */
class StoreBreadcrumb
{
    /**
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $type = $this->getRequestType();
        
        if($this->isListOrDashboardOrSingleShowRequest()) {
            // Simple: reset breadcrumb
            session()->put("sharp_breadcrumb", [
                [
                    "type" => $type,
                    "url" => $this->getFullUrl($request),
                    "name" => $this->determineBreadcrumbItemName(),
                    "entity_key" => $this->determineEntityKey()
                ]
            ]);

            return $next($request);
        }

        if($this->isShowRequest() || $this->isFormRequest()) {
            if(!$breadcrumb = session("sharp_breadcrumb")) {
                // Direct link case: we have to build a previous history.
                $breadcrumb = $this->buildPreviousState();

            } else {
                $segmentCount = sizeof($breadcrumb);
                $fullUrl = $this->getFullUrl($request);

                if ($segmentCount > 1 && $breadcrumb[$segmentCount - 2]["url"] == $fullUrl) {
                    // Navigate back: we must remove last breadcrumb segment
                    array_pop($breadcrumb);

                } elseif ($breadcrumb[$segmentCount - 1]["url"] != $fullUrl) {
                    // Navigate forward: append to breadcrumb
                    if($request->get("x-access-from") != "ui") {
                        // Direct URL case: we treat this as a new request, we reset the breadcrumb.
                        $breadcrumb = $this->buildPreviousState();

                    } else {
                        // "Pile up" case: we add the new URL to the breadcrumb.
                        $breadcrumb = $this->updatePreviousBreadcrumbItemWithReferer(
                            $breadcrumb,
                            $request->header("referer")
                        );
                        
                        $previousEntityKey = $this->determineEntityKey(
                            explode("/", parse_url($breadcrumb[sizeof($breadcrumb)-1]["url"])["path"])[3]
                        );
                        
                        $breadcrumb[] = [
                            "type" => $type,
                            "url" => $this->getFullUrl($request),
                            "name" => $this->determineBreadcrumbItemName($type, $previousEntityKey)
                        ];
                    }

                } // Else: current page reload. Do nothing.
            }

            session()->put("sharp_breadcrumb", $breadcrumb);
        }

        return $next($request);
    }

    private function getRequestType(): string
    {
        if($this->isListRequest()) {
            return "entityList";
        }

        if($this->isFormRequest()) {
            return "form";
        }

        if($this->isShowRequest() || $this->isSingleShowRequest()) {
            return "show";
        }

        if($this->isDashboardRequest()) {
            return "dashboard";
        }

        return "?";
    }

    private function isShowRequest(): bool
    {
        return request()->is(sprintf('%s/show/*/*', sharp_base_url_segment()));
    }

    private function isSingleShowRequest(): bool
    {
        return request()->is(sprintf('%s/show/*', sharp_base_url_segment())) && !$this->isShowRequest();
    }

    private function isFormRequest(): bool
    {
        return request()->is(sprintf('%s/form/*', sharp_base_url_segment()));
    }

    private function isSingleFormRequest(): bool
    {
        if(!$this->isFormRequest() || $this->determineInstanceId() !== null) {
            return false;
        } 
        
        return is_subclass_of(
            config("sharp.entities.{$this->determineEntityKey()}.form"), 
            SharpSingleForm::class
        );
    }

    private function isListRequest(): bool
    {
        return request()->is(sprintf('%s/list/*', sharp_base_url_segment()));
    }

    private function isDashboardRequest(): bool
    {
        return request()->is(sprintf('%s/dashboard/*', sharp_base_url_segment()));
    }

    private function isListOrDashboardOrSingleShowRequest(): bool
    {
        return $this->isListRequest()
            || $this->isDashboardRequest()
            || $this->isSingleShowRequest();
    }

    protected function determineEntityKey(string $key = null): ?string
    {
        $key = $key ?: request()->segment(3);

        return strpos($key, ":") !== false
            ? explode(":", $key)[0]
            : $key;
    }

    private function determineInstanceId(): ?string
    {
        return request()->segment(4);
    }

    protected function determineBreadcrumbItemName(string $type = null, string $previousEntityKey = null): string
    {
        $type = $type ?: $this->getRequestType();
        $currentEntityKey = $this->determineEntityKey();
        
        switch ($type) {
            case "entityList":
                return trans("sharp::breadcrumb.entityList");
            case "dashboard":
                return trans("sharp::breadcrumb.dashboard");
            case "show":
                return trans("sharp::breadcrumb.show", ["entity" => $this->determineEntityLabel($currentEntityKey)]);
            case "form":
                // A Form is always a leaf
                if($this->determineInstanceId() || $this->isSingleFormRequest()) {
                    if($previousEntityKey !== null && $previousEntityKey !== $currentEntityKey) {
                        // The form entityKey is different from the previous entityKey in the breadcrumb:
                        // we are in a EEL case.
                        return trans("sharp::breadcrumb.form.edit_entity", ["entity" => $this->determineEntityLabel($currentEntityKey)]);
                    }
                    return trans("sharp::breadcrumb.form.edit");
                }
                return trans("sharp::breadcrumb.form.create", ["entity" => $this->determineEntityLabel($currentEntityKey)]);
        }
        
        return $this->determineEntityLabel($currentEntityKey);
    }

    private function determineEntityLabel(?string $entityKey): string
    {
        return $entityKey 
            ? config("sharp.entities.$entityKey.label", $entityKey) 
            : "";
    }

    /**
     * We access through a direct link to Show or Form: we must build previous state,
     * from scratch, to handle future "Back" button calls.
     */
    private function buildPreviousState(): array
    {
        if($this->isShowRequest()) {
            // Build a List URL in previous history
            return [
                [
                    "type" => "entityList",
                    "url" => url(sprintf('%s/list/%s',
                        sharp_base_url_segment(),
                        $this->determineEntityKey()
                    )),
                    "name" => $this->determineBreadcrumbItemName("entityList"),
                    "entity_key" => $this->determineEntityKey()
                ], [
                    "type" => "show",
                    "url" => request()->fullUrl(),
                    "name" => $this->determineBreadcrumbItemName("show"),
                ]
            ];
        }

        // Form request is trickier: we could have a List, a List + Show or a SingleShow before
        if(config(sprintf('sharp.entities.%s.list', $this->determineEntityKey()))) {
            // Entity has a List configured
            $breadcrumb = [
                [
                    "type" => "entityList",
                    "url" => url(sprintf('%s/list/%s',
                        sharp_base_url_segment(),
                        $this->determineEntityKey()
                    )),
                    "name" => $this->determineBreadcrumbItemName("entityList"),
                    "entity_key" => $this->determineEntityKey()
                ]
            ];

            if(config(sprintf('sharp.entities.%s.show', $this->determineEntityKey()))) {
                // Entity has also a Show configured
                $breadcrumb[] = [
                    "type" => "show",
                    "url" => url(sprintf('%s/show/%s/%s',
                        sharp_base_url_segment(),
                        $this->determineEntityKey(),
                        $this->determineInstanceId()
                    )),
                    "name" => $this->determineBreadcrumbItemName("show"),
                ];
            }

        } else {
            // Then it MUST be a SingleShow case
            $breadcrumb = [
                [
                    "type" => "show",
                    "url" => url(sprintf('%s/show/%s',
                        sharp_base_url_segment(),
                        $this->determineEntityKey()
                    )),
                    "name" => $this->determineBreadcrumbItemName("show"),
                    "entity_key" => $this->determineEntityKey()
                ]
            ];
        }

        $breadcrumb[] = [
            "type" => $this->getRequestType(),
            "url" => request()->fullUrl(),
            "name" => $this->determineBreadcrumbItemName(),
        ];

        return $breadcrumb;
    }

    /**
     * Replace the last breadcrumb item with $referer if both
     * URL are the same minus the querystring (which we
     * know to be OK in the $referer URL).
     */
    private function updatePreviousBreadcrumbItemWithReferer($breadcrumb, $referer): array
    {
        if($referer && sizeof($breadcrumb)) {
            $lastItem = parse_url(Arr::last($breadcrumb)["url"]);
            $refererUrl = parse_url($referer);

            if ($lastItem["host"] == $refererUrl["host"] && $lastItem["path"] == $refererUrl["path"]) {
                $breadcrumb[sizeof($breadcrumb) - 1]["url"] = $referer;
            }
        }

        return $breadcrumb;
    }

    private function getFullUrl(Request $request): string
    {
        if($value = $request->get("x-access-from")) {
            // we have to strip the  "x-access-from=ui" part since we don't want
            // this technical attribute to be store in the breadcrumb.
            $url = Str::replaceFirst("x-access-from={$value}", "", $request->fullUrl());

            if(Str::endsWith($url, "?")) {
                return substr($url, 0, strlen($url) - 1);
            }

            return $url;
        }

        return $request->fullUrl();
    }
}
