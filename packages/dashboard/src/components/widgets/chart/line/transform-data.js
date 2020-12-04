import { normalizeColors } from "../../../../util/chart";

export function transformLineData(widgetValue) {
    const datasets = widgetValue?.datasets ?? [];
    const labels = widgetValue?.labels ?? [];

    return {
        series: datasets.map(dataset => ({
            data: dataset.data,
            name: dataset.label,
        })),
        colors: normalizeColors(datasets.map(dataset => dataset.color)),
        labels,
    }
}