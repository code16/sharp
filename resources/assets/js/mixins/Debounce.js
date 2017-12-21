export function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

export default {
    beforeCreate() {
        this.$options.computed = this.$options.computed || {};

        let { computed, debounced } = this.$options;

        if(debounced) {
            if(typeof debounced === 'function') {
                debounced = debounced(this.$options);
            }

            let wait = debounced.wait;
            if(wait == null) {
                console.warn('[Debounce mixin] Debounced option wait time not defined default : 200');
                wait = 200;
            }
            for(let key of Object.keys(debounced)) {
                if(typeof debounced[key] === 'function') {
                    if(key in computed) {
                        continue;
                    }
                    computed[key] = function() {
                        return debounce(debounced[key].bind(this),wait);
                    }
                }
            }
        }
    }
};