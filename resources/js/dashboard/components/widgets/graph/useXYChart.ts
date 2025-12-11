import { DashboardWidgetProps } from "@/dashboard/types";
import { GraphWidgetData } from "@/types";
import { computed } from "vue";
import { XYComponentConfigInterface } from "@unovis/ts/core/xy-component/config";

type Datum = number[];

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

    return { data, x, y };
}
