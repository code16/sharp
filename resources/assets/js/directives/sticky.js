import Vue from 'vue';
import debounce from 'lodash/debounce';

class StickyObserver {
    /**
     * @type HTMLElement
     */
    el;
    /**
     * @type IntersectionObserver
     */
    intersectionObserver;
    intersectionObserverStyle;
    resizeListener;
    stuck;

    observe(el) {
        this.el = el;
        this.resizeListener = debounce(() => this.handleResize(), 100, {
            leading: true,
            trailing: true,
            maxWait: 100,
        });
        this.placeholder = document.createElement('div');
        this.el.parentElement.insertBefore(this.placeholder, this.el);
        window.addEventListener('resize', this.resizeListener);
        this.updateIntersectionObserver(window.getComputedStyle(this.el));
    }

    destroy() {
        this.intersectionObserver?.disconnect();
        window.removeEventListener('resize', this.resizeListener);
    }

    handleResize() {
        this.refresh();
    }

    /**
     * @param {CSSStyleDeclaration} style
     */
    updateIntersectionObserver(style) {
        if(style.top === this.intersectionObserverStyle?.top) {
            return;
        }
        this.intersectionObserverStyle = style;
        this.intersectionObserver?.disconnect();
        const top = `${(parseInt(style.top) || 0) + 1}px`;
        this.intersectionObserver = new IntersectionObserver(
            ([e]) => {
                if(style.position === 'sticky') {
                    this.setStuck(e.intersectionRatio < 1 && e.rootBounds?.top === e.intersectionRect?.top);
                }
            },
            {
                threshold: [1],
                rootMargin: `-${top} 0px 0px 0px`
            }
        );
        this.intersectionObserver.observe(this.el);
    }

    async refresh() {
        const style = window.getComputedStyle(this.el);
        if(style.position === 'sticky') {
            await this.setStuck(parseInt(style.top) === this.el.getBoundingClientRect().top);
            this.updateIntersectionObserver(style);
        } else {
            await this.setStuck(false);
        }
    }

    async setStuck(stuck) {
        this.el.style.height = '';
        const height = this.el.offsetHeight;
        this.stuck = stuck;
        this.el.style.height = stuck ? `${height}px` : '';
        this.el.classList.toggle('stuck', stuck);
        this.el.dispatchEvent(new CustomEvent('sticky-change', { detail:stuck }));
        await Vue.nextTick();
        if(stuck) {
            this.el.style.height = '';
            this.el.style.setProperty('--sticky-offset', `${this.el.offsetHeight - height}px`);
            this.el.style.height = `${height}px`;
        }
    }
}

export default {
    inserted(el, bindings, vnode) {
        el._stickyObserver = new StickyObserver();
        el._stickyObserver.observe(el);

        if(bindings.expression) {
            el._stickyUnwatch = vnode.context.$watch(bindings.expression, () => {
                el._stickyObserver.handleResize();
            });
        }
    },
    unbind(el) {
        el._stickyObserver.destroy();
        el._stickyUnwatch();
    },
}
