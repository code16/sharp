<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
        if($this->isListOrDashboardOrSingleShowRequest()) {
            // Simple: reset breadcrumb
            session()->put("sharp_breadcrumb", [
                [
                    "type" => $this->getRequestType(),
                    "url" => $request->fullUrl()
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

                if ($segmentCount > 1 && $breadcrumb[$segmentCount - 2]["url"] == $request->fullUrl()) {
                    // Navigate back: we must remove last breadcrumb segment
                    array_pop($breadcrumb);

                } elseif ($breadcrumb[$segmentCount - 1]["url"] != $request->fullUrl()) {
                    // Navigate forward: append to breadcrumb
                    // TODO must handle direct URL case.
                    $breadcrumb = $this->updatePreviousBreadcrumbItemWithReferer(
                        $breadcrumb,
                        $request->header("referer")
                    );

                    $breadcrumb[] = [
                        "type" => $this->getRequestType(),
                        "url" => $request->fullUrl()
                    ];

                } // Else: current page reload. Do nothing.
            }

            session()->put("sharp_breadcrumb", $breadcrumb);
        }

        return $next($request);
    }

    /**
     * @return string
     */
    private function getRequestType()
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

    /**
     * @return boolean
     */
    private function isShowRequest()
    {
        return request()->is('sharp/show/*/*');
    }

    /**
     * @return boolean
     */
    private function isSingleShowRequest()
    {
        return request()->is('sharp/show/*') && !$this->isShowRequest();
    }

    /**
     * @return boolean
     */
    private function isFormRequest()
    {
        return request()->is('sharp/form/*/*');
    }

    /**
     * @return boolean
     */
    private function isListRequest()
    {
        return request()->is('sharp/list/*');
    }

    /**
     * @return boolean
     */
    private function isDashboardRequest()
    {
        return request()->is('sharp/dashboard/*');
    }

    /**
     * @return boolean
     */
    private function isListOrDashboardOrSingleShowRequest()
    {
        return $this->isListRequest()
            || $this->isDashboardRequest()
            || $this->isSingleShowRequest();
    }

    /**
     * @return string|null
     */
    protected function determineEntityKey()
    {
        $key = request()->segment(3);

        return strpos($key, ":") !== false
            ? explode(":", $key)[0]
            : $key;
    }

    /**
     * @return string|null
     */
    private function determineInstanceId()
    {
        return request()->segment(4);
    }

    /**
     * We access through a direct link to Show or Form: we must build previous state,
     * from scratch, to handle future "Back" button calls.
     */
    private function buildPreviousState()
    {
        if($this->isShowRequest()) {
            // Build a List URL in previous history
            return [
                [
                    "type" => "entityList",
                    "url" => url(sprintf('sharp/list/%s', $this->determineEntityKey()))
                ], [
                    "type" => "show",
                    "url" => request()->fullUrl()
                ]
            ];
        }

        // Form request is trickier: we could have a List, a List + Show or a SingleShow before
        if(config(sprintf('sharp.entities.%s.list', $this->determineEntityKey()))) {
            // Entity has a List configured
            $breadcrumb = [
                [
                    "type" => "entityList",
                    "url" => url(sprintf('sharp/list/%s', $this->determineEntityKey()))
                ]
            ];

            if(config(sprintf('sharp.entities.%s.show', $this->determineEntityKey()))) {
                // Entity has also a Show configured
                $breadcrumb[] = [
                    "type" => "show",
                    "url" => url(sprintf('sharp/show/%s/%s',
                        $this->determineEntityKey(),
                        $this->determineInstanceId()
                    ))
                ];
            }

        } else {
            // Then it MUST be a SingleShow case
            $breadcrumb = [
                [
                    "type" => "show",
                    "url" => url(sprintf('sharp/show/%s', $this->determineEntityKey()))
                ]
            ];
        }

        $breadcrumb[] = [
            "type" => $this->getRequestType(),
            "url" => request()->fullUrl()
        ];

        return $breadcrumb;
    }

    /**
     * Replace the last breadcrumb item with $referer if both
     * URL are the same minus the querystring (which we
     * know to be OK in the $referer URL).
     *
     * @param array $breadcrumb
     * @param string|null $referer
     * @return array
     */
    private function updatePreviousBreadcrumbItemWithReferer($breadcrumb, $referer)
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
}