import { debounce } from '../mixins/Debounce';

export default {
    bind(el, { modifier='width', expression }, vnode) {
        let property = modifier;
        el._updateHasOverflow = debounce(() => {
            if(property === 'width') {
                //console.log('has overflow',  el.scrollWidth > el.offsetWidth);
                vnode.context[expression] = el.scrollWidth > el.offsetWidth;
            }
            else if(property === 'height') {
                vnode.context[expression] = el.scrollHeight > el.offsetHeight;
            }
        },100);
        window.addEventListener('resize', el._updateHasOverflow);
    },
    inserted(el) {
        el._updateHasOverflow();
    },
    unbind(el) {
        window.removeEventListener('resize', el._updateHasOverflow);
    }
};