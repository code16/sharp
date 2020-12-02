import { normalizeColors } from "../../../../util/chart";

export function transformPieData(widgetValue) {
    const { datasets } = widgetValue;

    return {
        series: datasets.map(dataset => dataset.data[0]),
        colors: normalizeColors(datasets.map(dataset => dataset.color)),
        labels: datasets.reduce((res, dataset) => [
            ...res, dataset.label,
        ], []),
    }
}