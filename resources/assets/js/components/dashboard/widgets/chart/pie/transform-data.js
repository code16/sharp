
export function transformPieData(widgetValue) {
    const { datasets } = widgetValue;

    return {
        datasets: [
            datasets.reduce((res, dataset) => ({
                data: [...(res.data || []), dataset.data[0]],
                backgroundColor: [...(res.backgroundColor || []), dataset.color],
            }), {}),
        ],
        labels: datasets.reduce((res, dataset) => [
            ...res, dataset.label,
        ], []),
    }
}