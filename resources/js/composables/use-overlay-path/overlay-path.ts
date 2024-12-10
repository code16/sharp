// https://github.com/shepherd-pro/shepherd/blob/main/shepherd.js/src/utils/overlay-path.ts

interface OverlayPathParams {
    height: number;
    r?:
        | number
        | {
        bottomLeft: number;
        bottomRight: number;
        topLeft: number;
        topRight: number;
    };
    x?: number;
    y?: number;
    width: number;
}
export function makeOverlayPath({
    width,
    height,
    x = 0,
    y = 0,
    r = 0
}: OverlayPathParams) {
    const { innerWidth: w, innerHeight: h } = window;
    const {
        topLeft = 0,
        topRight = 0,
        bottomRight = 0,
        bottomLeft = 0
    } = typeof r === 'number'
        ? { topLeft: r, topRight: r, bottomRight: r, bottomLeft: r }
        : r;

    return `M${w},${h}\
H0\
V0\
H${w}\
V${h}\
Z\
M${x + topLeft},${y}\
a${topLeft},${topLeft},0,0,0-${topLeft},${topLeft}\
V${height + y - bottomLeft}\
a${bottomLeft},${bottomLeft},0,0,0,${bottomLeft},${bottomLeft}\
H${width + x - bottomRight}\
a${bottomRight},${bottomRight},0,0,0,${bottomRight}-${bottomRight}\
V${y + topRight}\
a${topRight},${topRight},0,0,0-${topRight}-${topRight}\
Z`;
}
