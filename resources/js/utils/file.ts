import { filesize } from 'filesize';


function getLocale() {
    return document.documentElement.lang.slice(0, 2) || 'en';
}

function getSymbols(locale) {
    if(locale === 'fr') {
        return {
            KB: 'Ko',
            MB: 'Mo',
        }
    }
}

// https://github.com/avoidwork/filesize.js
export function filesizeLabel(bytes: number) {
    const locale = getLocale();
    const exponent = Math.max(filesize(bytes, { output: 'exponent' }), 1);
    const resolvedBytes = Math.max(bytes, 128);
    const label = filesize(resolvedBytes, {
        standard: 'jedec',
        round: 2,
        exponent,
        locale: true,
        symbols: getSymbols(locale),
    });

    if(bytes < 128) {
        return `< ${label}`;
    }
    return label;
}
