export default {
    hyphenate(str) {
        return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    },
    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    log(str) {
        console.log(`SHARP : ${str}`, arguments.slice(1));
    },
    warn(str) {
        console.warn(`SHARP : ${str}`, arguments.slice(1));
    }
}