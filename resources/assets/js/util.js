export default {
    hyphenate(str) {
        return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    },
    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },
    callConsole(func, str, ...data) {
        console[func](`SHARP : ${str}`, ...data);
    },
    log() {
        this.callConsole('log', ...arguments);
    },
    warn() {
        this.callConsole('warn', ...arguments);
    },
    error() {
        this.callConsole('error', ...arguments);
    }
}