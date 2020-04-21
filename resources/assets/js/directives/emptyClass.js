function isHidden(vnode) {
    if(vnode.text) {
        return vnode.text.trim().length === 0;
    }
    if(vnode.elm instanceof HTMLElement) {
        return vnode.elm.style.display === 'none';
    }
    return !vnode.tag;
}

function emptyClass(el, { value }, vnode) {
    if(!vnode.children.length || vnode.children.every(vnode => isHidden(vnode))) {
        el.classList.add(value);
    }
    else {
        el.classList.remove(value);
    }
}

export default {
    inserted:emptyClass,
    componentUpdated:emptyClass
}