function parseValue(val) {
    if(val.indexOf(',')!==-1) {
        return val.split(',');
    }
    return val;
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

function serialize(obj) {
    return Object.keys(obj).reduce((res, key, index)=>{
        let value = serializeValue(obj[key]);
        return `${res}${index?'&':''}${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
    },'?');
}

export {
    parse,
    serialize
}