

export function getAllowedHeadingLevels(toolbar) {
    return toolbar
        .map(button => button.match(/^heading-(\d)$/))
        .filter(match => !!match)
        .map(match => Number(match[1]));
}

export function toolbarHasButton(toolbar, buttonName) {
    return toolbar.some(button => button === buttonName);
}
