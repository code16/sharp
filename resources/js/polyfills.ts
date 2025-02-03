
if(!window.crypto.randomUUID) {
    // reka-ui using it so polyfill in non HTTP environment (https://github.com/unovue/radix-vue/commit/d1633fc01b3db7da4049ac7c1ea994a9aa95a230#r150102680)
    // @ts-ignore
    window.crypto.randomUUID = function() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    }
}
