
export function getAppendableUri() {
    return location.pathname.replace(/^.+?s-list/, '');
}
