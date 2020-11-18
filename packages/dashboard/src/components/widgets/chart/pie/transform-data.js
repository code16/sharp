
export function transformPieData(widgetValue) {
    const { datasets } = widgetValue;

    return {
        series: datasets.map(dataset => dataset.data[0]),
        colors: datasets.map(dataset => dataset.color),
        labels: datasets.reduce((res, dataset) => [
            ...res, dataset.label,
        ], []),
    }
}