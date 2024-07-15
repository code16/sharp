

const canvasCtx = document.createElement('canvas').getContext('2d');

export function normalizeColor(color: string): string {
    canvasCtx.fillStyle = color;
    return canvasCtx.fillStyle;
}
