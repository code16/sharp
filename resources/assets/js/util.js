import Vue from 'vue';

export const hyphenate = str => str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
export const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1);
export const callConsole = (func, str, ...data) => console[func](`SHARP : ${str}`, ...data);
export const log = (...args) => callConsole('log', ...args);
export const warn = (...args) => callConsole('warn', ...args);
export const error = (...args) => callConsole('error', ...args);

export const ignoreWarns = callback => {
    Vue.config.silent = true;
    callback();
    Vue.config.silent = false;
};
