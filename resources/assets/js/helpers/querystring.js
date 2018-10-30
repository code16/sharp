function parseValue(val) {
    const res = val.replace(/\+/g, ' ');
    return res.includes(',') ? res.split(',') : res;
}

function parse() {
    if(location.search.length<2) return {};
    return location.search.substring(1).split('&').reduce((res, pair) => {
        let [ key, value ] = pair.split('=');
        res[decodeURIComponent(key)] = parseValue(decodeURIComponent(value));
        return res;
    },{});
}

function serializeValue(val) {
    if(Array.isArray(val)) {
        return val.join(',');
    }
    return val;
}

function serialize(obj, { urlSeparator=true }={}) {
    return Object.keys(obj).reduce((res, key, index)=>{
        let value = serializeValue(obj[key]);
        return `${res}${index?'&':''}${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
    },urlSeparator?'?':'');
}

export {
    parse,
    serialize
}