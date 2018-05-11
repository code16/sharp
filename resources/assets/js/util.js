import Vue from 'vue';

export const hyphenate = str => str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
export const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1);
export const callConsole = (func, str, ...data) => console[func](`SHARP : ${str}`, ...data);
export let log = (...args) => callConsole('log', ...args);
export let warn = (...args) => callConsole('warn', ...args);
export let error = (...args) => callConsole('error', ...args);

export const ignoreWarns = callback => {
    Vue.config.silent = true;
    callback();
    Vue.config.silent = false;
};

export function loadFontAwesome() {
    let script = document.getElementById('loadFA');
    if(script.hasAttribute('data-src')) {
        script.setAttribute('src', script.getAttribute('data-src'));
        script.removeAttribute('data-src');
    }
}