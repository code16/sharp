import { normalizeColors } from "../../../../util/chart";

export function transformBarData(widgetValue) {
    const { datasets, labels } = widgetValue;

    // const distributed = labels.length === 1;
    // if(distributed) {
    //     return {
    //         series: [
    //             {
    //                 data: datasets.map(dataset => dataset.data[0]),
    //             },
    //         ],
    //         colors: normalizeColors(datasets.map(dataset => dataset.color)),
    //         labels: datasets.map(dataset => dataset.label),
    //         distributed: true,
    //     }
    // }

    return {
        series: datasets.map(dataset => ({
            data: dataset.data,
            name: dataset.label,
        })),
        colors: normalizeColors(datasets.map(dataset => dataset.color)),
        labels,
    }
}