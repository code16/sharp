import { DashboardWidgetProps } from "@/dashboard/types";
import { GraphWidgetData } from "@/types";
import { computed } from "vue";
import { XYComponentConfigInterface } from "@unovis/ts/core/xy-component/config";
import { ColorAccessor, CrosshairConfigInterface } from "@unovis/ts";

export type Datum = number[];

export function useXYChart(props: DashboardWidgetProps<GraphWidgetData>) {
    const data = computed<Datum[]>(() => props.value?.datasets?.reduce((res, dataset, i) => {
        dataset.data.forEach((v, j) => {
            res[j] ??= [];
            res[j][i] = v;
        });
        return res;
    }, []));
    const x: XYComponentConfigInterface<Datum>['x'] = (d, i) => i;
    const y = computed<XYComponentConfigInterface<Datum>['y']>(() => props.value?.datasets.map((dataset, i) => (d) => d[i]));
    // const color: ColorAccessor<Datum | Datum[]> = (_, i) => props.value?.datasets[i]?.color;
    const color: ColorAccessor<Datum | Datum[]> = props.value?.datasets.map((dataset, i) => dataset.color);
    const tooltipTemplate: CrosshairConfigInterface<any>['template'] = function (d, x: number) {
        console.log(arguments);
        return `<div>${props.value.labels[Math.round(x)]}</div>
        ${
            props.value?.datasets.map((dataset, i) =>
                `<div class="flex items-center gap-2">
                    <span class="size-3 rounded-full bg-(--color)" style="--color: ${dataset.color}"></span>
                    ${dataset.label ? `<span>${dataset.label}:</span>` : ''}
                    ${d[i]}
                </div>`
            ).join('')
        }`;
    }

    return { data, x, y, color, tooltipTemplate };
}
