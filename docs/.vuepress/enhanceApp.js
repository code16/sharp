

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
}