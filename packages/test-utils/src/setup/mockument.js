global.Image = global.window.Image;

global.Range = function Range() {};

const createContextualFragment = html => {
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.children[0]; // so hokey it's not even funny
};

Range.prototype.createContextualFragment = html =>
    createContextualFragment(html);

// HACK: Polyfil that allows codemirror to render in a JSDOM env.
global.window.document.createRange = function createRange() {
    return {
        setEnd: () => {},
        setStart: () => {},
        getBoundingClientRect: () => ({ right: 0 }),
        getClientRects: () => [],
        createContextualFragment
    };
};

// prevent errors when modifying location props
delete window.location;
window.location = {
    set href(href) {},
    get origin() {
        return 'http://saturn.test';
    }
};