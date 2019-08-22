<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
            session()->put("sharp_breadcrumb", [$request->fullUrl()]);

            return $next($request);
        }

        if($this->isShowRequest() || $this->isFormRequest()) {
            if(!$breadcrumb = session("sharp_breadcrumb")) {
                // Direct link case: we have to build a previous history.
                $breadcrumb = $this->buildPreviousState();

            } else {
                $segmentCount = sizeof($breadcrumb);

                if ($segmentCount > 1 && $breadcrumb[$segmentCount - 2] == $request->fullUrl()) {
                    // Navigate back: we must remove last breadcrumb segment
                    array_pop($breadcrumb);

                } elseif ($breadcrumb[$segmentCount - 1] != $request->fullUrl()) {
                    // Navigate forward: append to breadcrumb
                    $breadcrumb[] = $request->fullUrl();

                } // Else: current page reload. Do nothing.
            }

            session()->put("sharp_breadcrumb", $breadcrumb);
        }

        return $next($request);
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
    private function isFormRequest()
    {
        return request()->is('sharp/form/*/*');
    }

    /**
     * @return boolean
     */
    private function isListOrDashboardOrSingleShowRequest()
    {
        return request()->is('sharp/list/*')
            || request()->is('sharp/dashboard/*')
            || (request()->is('sharp/show/*') && !$this->isShowRequest());
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
                url(sprintf('sharp/list/%s', $this->determineEntityKey())),
                request()->fullUrl()
            ];
        }

        // Form request is trickier: we could have a List, a List + Show or a SingleShow before
        if(config(sprintf('sharp.entities.%s.list', $this->determineEntityKey()))) {
            // Entity has a List configured
            $breadcrumb = [
                url(sprintf('sharp/list/%s', $this->determineEntityKey()))
            ];

            if(config(sprintf('sharp.entities.%s.show', $this->determineEntityKey()))) {
                // Entity has also a Show configured
                $breadcrumb[] = url(sprintf('sharp/show/%s/%s',
                    $this->determineEntityKey(),
                    $this->determineInstanceId()
                ));
            }

        } else {
            // Then it MUST be a SingleShow case
            $breadcrumb = [
                url(sprintf('sharp/show/%s', $this->determineEntityKey())),
            ];
        }

        $breadcrumb[] = request()->fullUrl();

        return $breadcrumb;
    }
}