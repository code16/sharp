import { usePage } from "@inertiajs/vue3";


export function useEntityListHighlightedItem() {
    const highlightedEntityKey = usePage().props.query?.highlighted_entity_key;
    const highlightedInstanceId = usePage().props.query?.highlighted_instance_id;
    // const url = new URL(location.href);
    // const highlightedEntityKey = url.searchParams.get('highlighted_entity_key');
    // const highlightedInstanceId = url.searchParams.get('highlighted_instance_id');
    //
    // url.searchParams.delete('highlighted_entity_key');
    // url.searchParams.delete('highlighted_instance_id');
    // url.searchParams.delete('popstate');

    // router.replace({
    //     url: url.href,
    //     preserveState: true,
    //     preserveScroll: true,
    // });

    return { highlightedEntityKey, highlightedInstanceId }
}
