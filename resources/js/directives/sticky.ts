import throttle from 'lodash/throttle';
import { Directive, nextTick } from "vue";

class StickyObserver {
    el: HTMLElement;
    sentinel: HTMLElement;
    scrollContainer: HTMLElement | Window;
    listener: () => void;
    position: 'bottom' | 'top';

    constructor(el) {
        this.el = el;
        this.listener = throttle(() => this.refresh(), 50);
        this.sentinel = document.createElement('div');
        this.scrollContainer = el.closest('.modal') ?? window;
        this.position = window.getComputedStyle(el).bottom !== 'auto' ? 'bottom' : 'top';

        this.sentinel.setAttribute('data-sticky-sentinel', '');
        if(this.position === 'bottom') {
            this.el.parentElement.insertBefore(this.sentinel, this.el.nextSibling);
        } else {
            this.el.parentElement.insertBefore(this.sentinel, this.el);
        }
        this.scrollContainer.addEventListener('scroll', this.listener);
        window.addEventListener('resize', this.listener);
        nextTick(() => this.refresh());
    }

    destroy() {
        this.el = null;
        this.sentinel.remove();
        this.scrollContainer.removeEventListener('scroll', this.listener);
        window.removeEventListener('resize', this.listener);
    }

    refresh() {
        if(!this.el) {
            return;
        }
        const rect = this.el.getBoundingClientRect();
        const anchor = this.el.querySelector('[data-sticky-anchor]');

        if(this.position === 'bottom') {
            this.setStuck(rect.bottom < this.sentinel.getBoundingClientRect().bottom);
        } else {
            this.setStuck(rect.top > this.sentinel.getBoundingClientRect().top);
        }

        if(anchor) {
            this.el.style.setProperty('--sticky-offset', `${rect.top - anchor.getBoundingClientRect().top}px`);
        }
    }

    setStuck(stuck) {
        this.el.classList.toggle('stuck', stuck);
        this.el.dispatchEvent(new CustomEvent('stuck-change', { detail:stuck }));
    }
}

export const vSticky = {
    mounted(el, { value }) {
        if(typeof value === 'undefined' ? true : value) {
            el._stickyObserver = new StickyObserver(el);
        }
    },
    updated(el, { value }) {
        if(value && !el._stickyObserver) {
            el._stickyObserver = new StickyObserver(el);
        }
        el._stickyObserver?.refresh();
    },
    unmounted(el) {
        el._stickyObserver?.destroy();
    },
} satisfies Directive;
