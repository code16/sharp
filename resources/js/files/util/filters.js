

function serializeNumber(number, decimals) {
    if(typeof number !== 'number') {
        return null;
    }

    return Math.round((number + Number.EPSILON) * 10 ** decimals) / 10 ** decimals;
}

export function serializeFilterNumber(number, decimals) {
    return serializeNumber(number, decimals);
}

export function serializeFilterCrop(data) {
    let { x, y, width, height } = data ?? {};

    x = serializeNumber(x, 4) ?? 0;
    y = serializeNumber(y, 4) ?? 0;
    width = serializeNumber(width, 4) ?? 1;
    height = serializeNumber(height, 4) ?? 1;

    if(x === 0 && y === 0 && width === 1 && height === 1) {
        return null;
    }

    return `${x},${y},${width},${height}`;
}

export function parseFilterCrop(attributeValue) {
    if(!attributeValue) {
        return null;
    }

    const [x, y, width, height] = attributeValue.split(',');

    return {
        x: Number(x),
        y: Number(y),
        width: Number(width),
        height: Number(height),
    }
}


export function serializeFilterRotate(data) {
    return serializeNumber(data?.angle, 4) || null;
}


export function parseFilterRotate(attributeValue) {
    if(!attributeValue) {
        return null;
    }

    return {
        angle: Number(attributeValue),
    }
}
