import { ShowLayoutFieldData } from "@/types";

export type FieldProps = {
    layout?: ShowLayoutFieldData,
    locale: string,
    entityKey: string,
    instanceId: string,
    collapsable?: boolean,
    root?: boolean,
}
