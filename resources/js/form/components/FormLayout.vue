<script setup lang="ts">
    import { ref } from "vue";
    import { slugify } from "@/utils";
    import { Form } from "../Form";
    import { router } from "@inertiajs/vue3";
    import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
    import { Select, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
    import { Badge } from "@/components/ui/badge";

    const props = defineProps<{
        form: Form,
    }>();

    const selectedTabSlug = ref(null);

    if(props.form.layout.tabbed && props.form.layout.tabs.length > 1) {
        selectedTabSlug.value = props.form.layout.tabs
                .map(tab => slugify(tab.title))
                .find(tabSlug => new URLSearchParams(location.search).get('tab') == tabSlug)
            ?? slugify(props.form.layout.tabs?.[0]?.title ?? '');
    }

    function onTabChange(tabSlug: string) {
        selectedTabSlug.value = tabSlug;
        const url = location.origin + location.pathname + `?tab=${tabSlug}`;
        router.page.url = url;
        history.replaceState(router.page, null, url);
    }
</script>
<template>
    <template v-if="form.layout.tabbed && form.layout.tabs.length > 1">
        <Tabs :model-value="selectedTabSlug" @update:model-value="onTabChange">
            <div class="sm:hidden">
                <Select
                    :model-value="selectedTabSlug"
                    @update:model-value="onTabChange"
                >
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <template v-for="tab in form.layout.tabs">
                        <SelectItem :value="slugify(tab.title)">
                            {{ tab.title }}
                        </SelectItem>
                    </template>
                </Select>
            </div>
            <div class="hidden sm:block">
                <TabsList>
                    <template v-for="tab in form.layout.tabs">
                        <TabsTrigger :value="slugify(tab.title)">
                            {{ tab.title }}
                            <template v-if="form.tabErrorsCount(tab)">
                                <Badge variant="destructive">
                                    {{ form.tabErrorsCount(tab) }}
                                </Badge>
                            </template>
                        </TabsTrigger>
                    </template>
                </TabsList>
            </div>

            <template v-for="tab in form.layout.tabs">
                <TabsContent :value="slugify(tab.title)">
                    <slot :tab="tab" />
                </TabsContent>
            </template>
        </Tabs>
    </template>
    <template v-else>
        <template v-for="tab in form.layout.tabs">
            <slot :tab="tab" />
        </template>
    </template>
</template>
