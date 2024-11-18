

function round(number: number, decimals = 4) {
    return Math.round((number + Number.EPSILON) * 10 ** decimals) / 10 ** decimals;
}

export function getCropDataFromFilters({ filters, imageWidth, imageHeight }) {
    const rotate = filters?.rotate?.angle ?? 0;
    let rw = imageWidth, rh = imageHeight;

    if(Math.abs(rotate) % 180) {
        rw = imageHeight;
        rh = imageWidth;
    }

    const { width, height, x, y } = filters?.crop ?? {};

    return {
        width: Math.round((width ?? 1) * rw),
        height: Math.round((height ?? 1) * rh),
        x: Math.round((x ?? 0) * rw),
        y: Math.round((y ?? 0) * rh),
        rotate: rotate * -1,
    }
}

export function getFiltersFromCropData({ cropData, imageWidth, imageHeight }) {
    let rw = imageWidth, rh = imageHeight;

    if(Math.abs(cropData.rotate) % 180) {
        rw = imageHeight;
        rh = imageWidth;
    }

    return {
        crop: {
            width: round(cropData.width / rw),
            height: round(cropData.height / rh),
            x: round(cropData.x / rw),
            y: round(cropData.y / rh),
        },
        rotate: {
            angle: cropData.rotate ? cropData.rotate * -1 : 0,
        },
    }
}
