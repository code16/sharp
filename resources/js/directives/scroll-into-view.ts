import { Directive, nextTick } from "vue";

export function scrollIntoViewIfNeeded(el: HTMLElement) {
    const scrollParent = el.closest('.overflow-auto, .overflow-x-auto, .overflow-y-auto');
    if(scrollParent) {
        if(scrollParent.scrollWidth > scrollParent.clientWidth || scrollParent.scrollHeight > scrollParent.clientHeight) {
            if(el.offsetTop < scrollParent.scrollTop || el.offsetTop + el.offsetHeight > scrollParent.scrollTop + scrollParent.clientHeight) {
                el.scrollIntoView({ block: 'start', inline: 'start' });
            }
        }
    }
}

export const vScrollIntoView: Directive = (el, { value, oldValue }) => {
    if(value && !oldValue) {
        console.log(value);
        setTimeout(() => {
            scrollIntoViewIfNeeded(el);
        });
    }
}
