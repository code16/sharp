
function formatMessage(str) {
    return `SHARP : ${str}`;
}

export function log(message, ...args) {
    console.log(formatMessage(message), ...args);
}

export function warn(message, ...args) {
    console.warn(formatMessage(message), ...args);
}

export function logError(message, ...args) {
    console.error(formatMessage(message), ...args);
}