

export function getBackUrl(breadcrumb) {
    const item = breadcrumb[breadcrumb.length - 2];
    return item ? item.url : null;
}
