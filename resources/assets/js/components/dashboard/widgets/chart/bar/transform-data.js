
export function transformBarData(widgetValue) {
    const { datasets, labels } = widgetValue;

    return {
        datasets: datasets.map(dataset => ({
            data: dataset.data,
            label: dataset.label,
            backgroundColor: dataset.color,
        })),
        labels,
    }
}