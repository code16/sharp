import { getToolbarIcon } from "../../../util/icons";

export const buttons = {
    bold: {
        attribute: 'bold',
        icon: getToolbarIcon('bold'),
    },
    italic: {
        attribute: 'italic',
        icon: getToolbarIcon('italic')
    },
    strike: {
        attribute: 'strike',
        icon: getToolbarIcon('strike')
    },
    link: {
        action: 'link',
        attribute: 'href',
        icon: getToolbarIcon('link')
    },
    heading1: {
        attribute: 'heading1',
        icon: getToolbarIcon('h1')
    },
    quote: {
        attribute: 'quote',
        icon: getToolbarIcon('quote')
    },
    code: {
        attribute: 'code',
        icon: getToolbarIcon('code')
    },
    bullet: {
        attribute: 'bullet',
        icon: getToolbarIcon('ul')
    },
    number: {
        attribute: 'number',
        icon: getToolbarIcon('ol')
    },
    increaseNestingLevel: {
        action: 'increaseNestingLevel',
        icon: getToolbarIcon('indent')
    },
    decreaseNestingLevel: {
        action: 'decreaseNestingLevel',
        icon: getToolbarIcon('de-indent')
    },
    undo: {
        action: 'undo',
        icon: getToolbarIcon('undo')
    },
    redo: {
        action: 'redo',
        icon: getToolbarIcon('redo')
    }
};