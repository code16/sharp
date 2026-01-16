import {  WidgetData } from "@/types";


export type DashboardWidgetProps<Data extends WidgetData = WidgetData, Value = Data['value']> = {
    widget: Data extends any ? Omit<Data, 'value'> : never;
    value: Value;
}
