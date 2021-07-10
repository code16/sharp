

function serializeNumber(number, decimals) {
    if(typeof number !== 'number') {
        return null;
    }

    return Math.round((number + Number.EPSILON) * 10 ** decimals) / 10 ** decimals;
}

export function serializeFilterCrop(value) {
    if(!value?.filters?.crop) {
        return null;
    }

    let { x, y, width, height } = value.filters.crop;

    x = serializeNumber(x, 4) ?? 0;
    y = serializeNumber(y, 4) ?? 0;
    width = serializeNumber(width, 4) ?? 1;
    height = serializeNumber(height, 4) ?? 1;

    if(x === 0 && y === 0 && width === 1 && height === 1) {
        return null;
    }

    return `${x},${y},${width},${height}`;
}


export function serializeFilterRotate(value) {
    if(!value?.filters?.rotate) {
        return null;
    }

    let { angle } = value.filters.rotate;

    return angle || null;
}
