import { entitiesMatch } from "@/utils/breadcrumb";
import { ShowEntityListFieldData } from "@/types";

export default function useFocusedItem(field: ShowEntityListFieldData): number | undefined {
    if(!document.referrer) {
        return;
    }
    const referrerUrl = new URL(document.referrer);
    if(referrerUrl.origin !== location.origin) {
        return;
    }

    const { entityKey, instanceId } = route(undefined, undefined, undefined, {
        ...Ziggy,
        // @ts-ignore
        location: referrerUrl,
    }).params;

    if(entityKey
        && entitiesMatch(entityKey, field.entityListKey)
        && instanceId
        && referrerUrl.pathname.length > location.pathname.length
    ) {
        return Number(instanceId);
    }
}
