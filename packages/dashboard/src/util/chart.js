

export function defaultChartOptions() {
    return {
        chart: {
            toolbar: {
                show: false,
            },
        },
        legend: {
            showForSingleSeries: true,
        },
    }
}

export function hasLegends(options) {
    return !!(options.legend?.show ?? true)
}

export function normalizeColors(colors) {
    const ctx = document.createElement('canvas').getContext('2d');
    return colors.map(color => {
        ctx.fillStyle = color;
        return ctx.fillStyle
    });
}