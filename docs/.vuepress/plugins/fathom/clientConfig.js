import { defineClientConfig } from '@vuepress/client';
import * as Fathom from 'fathom-client';

export default defineClientConfig({
    enhance({ router }) {
        if (process.env.NODE_ENV === 'production' && FATHOM_ID && typeof window !== 'undefined') {
            Fathom.load(FATHOM_ID, {
                url: FATHOM_URL,
                includedDomains: FATHOM_DOMAINS ? FATHOM_DOMAINS.split(',') : undefined,
            });

            router.afterEach(function(to) {
                Fathom.trackPageview();
            });
        }
    }
});
