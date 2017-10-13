function wrap(content) {
    return `<!--document part-->
${content}
<!--end document part-->`
}

function printHTML(content='<!-- undefined outerHTML -->') {
    return wrap(require('./htmlSnapshotBeautifier').print(content));
}

module.exports = {
    test(object) {
        return object instanceof HTMLElement || object instanceof HTMLCollection || object instanceof NodeList;
    },
    print(val, print, opts, colors){
        if(val instanceof HTMLCollection || val instanceof NodeList) {
            let array = [...val];
            return array.reduce((res, elm)=>`${res}${res?'\n':''}${printHTML(elm.outerHTML)}`, '');
        }
        return printHTML(val.outerHTML);
    }
};
