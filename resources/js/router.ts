import { router } from "@inertiajs/vue3";
import { VisitOptions } from "@inertiajs/core";

const state = {
    hasPoppedState: false,
}

export function hasPoppedState() {
    return state.hasPoppedState;
}

export function initRouter() {
    // force reload on previous navigation to invalidate outdated data / state
    window.addEventListener('popstate', (e) => {
        // console.time('popstate');
        // console.time('prefetching');
        // console.time('prefetched');
        const url = new URL(location.href);
        url.searchParams.set('popstate', '1');
        state.hasPoppedState = true;
        const params: VisitOptions = {
            headers: {
                'X-PopState': 'true'
            },
            preserveScroll: true,
            preserveState: false,
            replace: true,
            method: 'get',
            // onPrefetching() {
            //     console.timeEnd('prefetching');
            // },
            onSuccess(e) {
                // const url = new URL(location.href);
                // url.searchParams.delete('popstate');
                // router.replace({
                //     url: url.href,
                //     preserveState: true,
                //     preserveScroll: true,
                // });
                document.body.style.minHeight = '';
            },
        };
        router.prefetch(url, params, { cacheFor: 0 });
        document.addEventListener('inertia:navigate', () => {
            state.hasPoppedState = false;
            document.body.style.minHeight = `${document.body.clientHeight}px`;
            router.visit(url, params);
        }, { once: true });
    });

// on server error (e.g. 500) we want to visit errored page for debugging purposes
    router.on('invalid', event => {
        const response = event.detail.response;
        if(response.config.method.toLowerCase() === 'get') {
            location.href = event.detail.response.config.url;
        }
    });
}
