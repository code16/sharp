import { injectDialogRootContext } from "reka-ui";
import { useEventListener } from "@vueuse/core";

export function useForceBodyScrollLock() {
    const context = injectDialogRootContext();
    let savedScrollY = 0;
    useEventListener(document, 'scroll', (e) => {
        if(context.open.value) {
            window.scrollTo({ top: savedScrollY, behavior: 'instant' });
        } else {
            savedScrollY = window.scrollY;
        }
    });
}
