

export function getBackUrl(breadcrumb) {
    const item = breadcrumb[breadcrumb.length - 2];
    return item ? item.url : null;
}

export function entitiesMatch(entityKeyA, entityKeyB) {
    if(!entityKeyA || !entityKeyB) {
        return false;
    }
    return entityKeyA.replace(/:(.*)/, '') === entityKeyB.replace(/:(.*)/, '');
}
