function parse() {
    if(location.search.length<2) return {};
    return location.search.substring(1).split('&').reduce((res, pair) => {
        let [ key, value ] = pair.split('=');
        res[decodeURIComponent(key)] = decodeURIComponent(value);
        return res;
    },{});
}

function serialize(obj) {
    return Object.keys(obj).reduce((res, key, index)=>{
        return `${res}${index?'&':''}${encodeURIComponent(key)}=${encodeURIComponent(obj[key])}`;
    },'?');
}

export {
    parse,
    serialize
}