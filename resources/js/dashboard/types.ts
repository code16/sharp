import {  WidgetData } from "@/types";


export type DashboardWidgetProps<Data extends WidgetData = WidgetData, Value = Data['value']> = {
    widget: Omit<Data, 'value'>;
    value: Value;
}
