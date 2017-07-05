function parse() {
    if(location.search.length<2) return {};
    return decodeURIComponent(location.search).substring(1).split('&').reduce((res, pair) => {
        let [ key, value ] = pair.split('=');
        res[key] = value;
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