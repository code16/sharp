

export function getCropDataFromFilters({ filters, imageWidth, imageHeight }) {
    const rotate = filters?.rotate?.angle ?? 0;
    let rw = imageWidth, rh = imageHeight;

    if(Math.abs(rotate) % 180) {
        rw = imageHeight;
        rh = imageWidth;
    }

    const { width, height, x, y } = filters?.crop ?? {};

    return {
        width: (width ?? 1) * rw,
        height: (height ?? 1) * rh,
        x: (x ?? 0) * rw,
        y: (y ?? 0) * rh,
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
            width: cropData.width / rw,
            height: cropData.height / rh,
            x: cropData.x / rw,
            y: cropData.y / rh,
        },
        rotate: {
            angle: cropData.rotate * -1,
        },
    }
}
