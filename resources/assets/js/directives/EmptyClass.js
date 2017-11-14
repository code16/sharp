function emptyClass(el, { value }, vnode) {
    if(!vnode.children.length || !vnode.children.some(c=>c.tag)) {
        el.classList.add(value);
    }
    else el.classList.remove(value);
}

export default {
    inserted:emptyClass,
    componentUpdated:emptyClass
}