import { LayoutFieldData } from "@/types";

export type FieldProps = {
    layout?: LayoutFieldData,
    locale: string,
    entityKey: string,
    instanceId: string,
    collapsable?: boolean,
    root?: boolean,
}
