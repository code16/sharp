


export function getNavbarHeight() {
    const height = document.documentElement.style.getPropertyValue('--navbar-height');
    return parseInt(height) || 0;
}
