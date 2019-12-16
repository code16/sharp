

export default function ({ router, siteData }) {
    siteData.themeConfig.algolia.transformData = function(hits) {
        try {
            hits.forEach(hit => {
                // remove function parameters
                if(hit.hierarchy.lvl3) {
                    hit.hierarchy.lvl3 = hit.hierarchy.lvl3.replace(/\s+\(.+\)$/, '');
                    if(hit._highlightResult) {
                        const highlight = hit._highlightResult.hierarchy.lvl3;
                        highlight.value = highlight.value.replace(/\s+\([^<]+\)$/, '');
                    }
                }
            });
        } catch(e) {
            console.error(e);
        }
    };

    // https://github.com/vuejs/vuepress/issues/1802
    router.beforeEach((to, from, next) => {
        if(to.path && /^\/docs/.test(to.path)) {
            next({ path:to.path.replace(/^\/docs/, ''), hash:to.hash });
        } else {
            next();
        }
    });
}