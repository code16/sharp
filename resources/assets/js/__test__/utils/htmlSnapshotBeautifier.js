const beautify = require('js-beautify').html;

module.exports = {
    test(object) {
        return typeof object == 'string' && object.trim()[0] === '<';
    },
    print(val, print, opts, colors){
        return beautify(val, {});
    }
};
