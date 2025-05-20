import { Directive, nextTick } from "vue";

export function scrollIntoViewIfNeeded(el: HTMLElement, callback: () => void) {
    const scrollParent = el.closest('.overflow-auto, .overflow-x-auto, .overflow-y-auto');
    if(scrollParent) {
        if(scrollParent.scrollWidth > scrollParent.clientWidth || scrollParent.scrollHeight > scrollParent.clientHeight) {
            const o = new IntersectionObserver(e => {
                if(!e[0].isIntersecting) {
                    callback();
                }
                o.disconnect();
            }, { threshold: 1 })
            o.observe(el);
        }
    }
}

export const vScrollIntoView: Directive<any, boolean, ScrollLogicalPosition> = (el: HTMLElement, { value, oldValue, modifiers }) => {
    if(value && !oldValue) {
        setTimeout(() => {
            scrollIntoViewIfNeeded(el, () => {
                const align = modifiers.center ? 'center' : modifiers.nearest ? 'nearest' : 'start';
                el.scrollIntoView({ block: align, inline: align });
            });
        });
    }
}

// Temp directive to fix reka-ui scrollIntoView issues
export const vScrollIntoViewOverride: Directive<any, boolean, ScrollLogicalPosition> = {
    beforeMount(el: HTMLElement & { _scrollIntoView: HTMLElement['scrollIntoView'] }) {
        el._scrollIntoView = el.scrollIntoView;
        el.scrollIntoView = () => {
            setTimeout(() => {
                scrollIntoViewIfNeeded(el, () => {
                    el._scrollIntoView({ block: 'nearest', inline: 'nearest' });
                });
            });
        }
    }
}
