import { watch, useModel } from "vue";
import { slugify } from "@/utils";
import { router } from "@inertiajs/vue3";
import { FormData } from "@/types";

export function useFormTabs(props: { form: FormData, tab?: string }) {
    const selectedTabSlug = useModel(props, 'tab');

    selectedTabSlug.value = props.form.layout.tabs
            .map(tab => slugify(tab.title))
            .find(tabSlug => new URLSearchParams(location.search).get('tab') == tabSlug)
        ?? slugify(props.form.layout.tabs?.[0]?.title ?? '');

    if(props.form.layout.tabs.length > 1) {
        watch(selectedTabSlug, () => {
            const url = new URL(location.href);
            url.searchParams.set('tab', selectedTabSlug.value);
            router.replace({
                url: url.href,
                preserveState: true,
                preserveScroll: true,
            });
        }, { immediate: true });
    }

    return {
        selectedTabSlug,
    }
}
