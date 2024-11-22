import { Directive, nextTick } from "vue";

export function scrollIntoViewIfNeeded(el: HTMLElement, align: ScrollLogicalPosition = 'start') {
    const scrollParent = el.closest('.overflow-auto, .overflow-x-auto, .overflow-y-auto');
    if(scrollParent) {
        if(scrollParent.scrollWidth > scrollParent.clientWidth || scrollParent.scrollHeight > scrollParent.clientHeight) {
            const o = new IntersectionObserver(e => {
                if(!e[0].isIntersecting) {
                    el.scrollIntoView({ block: align, inline: align });
                }
                o.disconnect();
            }, { threshold: 1 })
            o.observe(el);
        }
    }
}

export const vScrollIntoView: Directive<any, boolean, ScrollLogicalPosition> = (el, { value, oldValue, modifiers }) => {
    if(value && !oldValue) {
        setTimeout(() => {
            scrollIntoViewIfNeeded(el, modifiers.center ? 'center' : 'start');
        });
    }
}
