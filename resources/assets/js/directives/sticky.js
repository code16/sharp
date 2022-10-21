import debounce from 'lodash/debounce';

class StickyObserver {
    /**
     * @type HTMLElement
     */
    el;
    listener;

    async observe(el) {
        this.el = el;
        this.listener = debounce(() => this.refresh(), 100, {
            leading: true,
            trailing: true,
            maxWait: 100,
        });
        this.sentinel = document.createElement('div');
        this.sentinel.dataset.stickySentinel = true;
        this.el.parentElement.insertBefore(this.sentinel, this.el);

        window.addEventListener('scroll', this.listener);
        window.addEventListener('resize', this.listener);
    }

    destroy() {
        window.removeEventListener('scroll', this.listener);
        window.removeEventListener('resize', this.listener);
    }

    refresh() {
        const rect = this.el.getBoundingClientRect();
        const anchor = this.el.querySelector('[data-sticky-anchor]');

        this.setStuck(rect.top > this.sentinel.getBoundingClientRect().top);

        if(anchor) {
            this.el.style.setProperty('--sticky-offset', `${rect.top - anchor.getBoundingClientRect().top}px`);
        }
    }

    setStuck(stuck) {
        this.el.classList.toggle('stuck', stuck);
        this.el.dispatchEvent(new CustomEvent('sticky-change', { detail:stuck }));
    }
}

export default {
    inserted(el, bindings, vnode) {
        el._stickyObserver = new StickyObserver();
        el._stickyObserver.observe(el);

        if(bindings.expression) {
            el._stickyUnwatch = vnode.context.$watch(bindings.expression, () => {
                el._stickyObserver.refresh();
            });
        }
    },
    unbind(el) {
        el._stickyObserver.destroy();
        el._stickyUnwatch();
    },
}
