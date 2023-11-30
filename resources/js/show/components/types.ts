import { LayoutFieldData } from "@/types";

export type ShowFieldProps = {
    layout?: LayoutFieldData,
    locale: string,
    entityKey: string,
    instanceId: string,
    collapsable?: boolean,
    root?: boolean,
}
