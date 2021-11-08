

export default {
    inserted(el) {
        const style =  window.getComputedStyle(el);
        if(style.position === 'sticky') {
            const top = `${(parseInt(style.top) || 0) + 1}px`;
            const observer = new IntersectionObserver(
                ([e]) => e.target.classList.toggle('stuck', e.intersectionRatio < 1 && e.rootBounds?.top === e.intersectionRect?.top),
                {
                    threshold: [1],
                    rootMargin: `-${top} 0px 0px 0px`
                }
            );
            observer.observe(el);
        }
    }
}
