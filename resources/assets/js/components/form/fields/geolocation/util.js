export function dd2dms(D, lng){
    let dir = D<0?lng?'W':'S':lng?'E':'N',
        deg = 0|(D<0?D=-D:D),
        min = 0|D%1*60,
        sec = (0|D*60%1*6000)/100;

    return `${deg}°${min}"${sec}' ${dir}`;
}


export function providerName(providerData) {
    return providerData
        ? providerData.name
        : null;
}

export function providerOptions(providerData) {
    return providerData
        ? providerData.options
        : {};
}

export function tilesUrl(mapsOptions) {
    return mapsOptions
        ? mapsOptions.tilesUrl
        : null;
}