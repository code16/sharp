function validate(binding) {
    if (typeof binding.value !== 'function') {
        console.warn('[Vue-click-outside:] provided expression', binding.expression, 'is not a function.');
        return false
    }
    return true
}

export default {
    bind: function (el, binding, vNode) {
        if (!validate(binding)) return;

        // Define Handler and cache it on the element
        function handler(e) {
            if (!vNode.context) return;
            if (!el.contains(e.target)) el.__vueClickOutside__.callback(e)
        }

        // add Event Listeners
        el.__vueClickOutside__ = {
            handler: handler,
            callback: binding.value
        };
        document.addEventListener('click', handler);
    },

    update: function (el, binding) {
        if (validate(binding)) el.__vueClickOutside__.callback = binding.value;
    },

    unbind: function (el) {
        // Remove Event Listeners
        document.removeEventListener('click', el.__vueClickOutside__.handler);
        delete el.__vueClickOutside__;
    }
}
