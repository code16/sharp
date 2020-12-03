import { normalizeColors } from "../../../../util/chart";

export function transformPieData(widgetValue) {
    const datasets = (widgetValue?.datasets ?? [])
        .filter(dataset => dataset.data?.length > 0);

    return {
        series: datasets.map(dataset => dataset.data[0]),
        colors: normalizeColors(datasets.map(dataset => dataset.color)),
        labels: datasets.map(dataset => dataset.label ?? ''),
    }
}