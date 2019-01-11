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

export function parseBlobJSONContent(blob) {
    return new Promise(resolve => {
        let reader = new FileReader();
        reader.addEventListener("loadend", function() {
            resolve(JSON.parse(reader.result));
        });
        reader.readAsText(blob);
    });
}

export function getFileName(headers={}) {
    let { ['content-disposition']: disposition } = headers;
    if (disposition && disposition.includes('attachment')) {
        let filenameRE = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        let matches = filenameRE.exec(disposition);
        if (matches != null && matches[1]) {
            return matches[1].replace(/['"]/g, '');
        }
    }
    return null;
}

export function getBaseUrl() {
    const meta = document.head.querySelector('meta[name=base-url]');
    return meta ? `/${meta.content}` : '/sharp';
}