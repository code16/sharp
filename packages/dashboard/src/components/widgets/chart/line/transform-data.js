
export function transformLineData(widgetValue) {
    const { datasets, labels } = widgetValue;

    return {
        datasets: datasets.map(dataset => ({
            data: dataset.data,
            label: dataset.label,
            borderColor: dataset.color,
            fill: false,
        })),
        labels,
    }
}