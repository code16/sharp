export default {
    isTemplateProp(fieldProp) {
        return /^.+Template$/.test(fieldProp);
    },
    hyphenate(str) {
        return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    },
    parseTemplateName(fieldProp) {
        let result=/^(.+)Template$/.exec(fieldProp);
        if(result.length>1)
            return this.hyphenate(result[1]);
        return null;
    }
}
