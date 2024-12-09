<script setup lang="ts">
    import { SidebarGroup, SidebarGroupContent, SidebarInput } from "@/components/ui/sidebar";
    import { Label } from "@/components/ui/label";
    import { config } from "@/utils/config";
    import { ref, watch } from "vue";
    import { Search } from "lucide-vue-next";
    import { Link } from '@inertiajs/vue3';
    import {
        Command,
        CommandGroup,
        CommandInput,
        CommandItem,
        CommandList
    } from "@/components/ui/command";
    import { useMagicKeys } from "@vueuse/core";
    import { SearchResultSetData } from "@/types";
    import { __ } from '@/utils/i18n';
    import Icon from "@/components/ui/Icon.vue";
    import { api } from "@/api/api";
    import { route } from "@/utils/url";
    import debounce from "lodash/debounce";
    import { Dialog, DialogScrollContent } from "@/components/ui/dialog";

    const open = ref(false);
    const loading = ref(false);
    const resultSets = ref<SearchResultSetData[]>([]);
    const searchTerm = ref('');
    const keys = useMagicKeys({
        passive: false,
    });
    watch(keys['Cmd+K'], (k) => {
        if(k) {
            open.value = !open.value;
        }
    });

    let loadingTimeout: any;
    async function search(query: string) {
        if(!query.length) {
            resultSets.value = [];
            return;
        }

        clearTimeout(loadingTimeout);
        loadingTimeout = setTimeout(() => {
            loading.value = true;
        });
        resultSets.value = await api.get(route('code16.sharp.api.search.index', { q: query }))
            .then(response => response.data)
            .finally(() => {
                clearTimeout(loadingTimeout);
                loading.value = false;
            });
    }

    const debouncedSearch = debounce(search, 200);
</script>

<template>
    <SidebarGroup v-bind="$attrs">
        <SidebarGroupContent class="relative">
            <Label for="global-search" class="sr-only">{{ config('sharp.search.placeholder') }}</Label>
            <SidebarInput
                id="global-search"
                class="pl-8 text-xs"
                :placeholder="config('sharp.search.placeholder') ?? __('sharp::menu.global_search.default_placeholder')"
                @click="open = true"
                readonly
            />
            <Search class="pointer-events-none absolute left-2 top-1/2 size-4 -translate-y-1/2 select-none opacity-50" />
        </SidebarGroupContent>
    </SidebarGroup>
    <Dialog v-model:open="open" @update:open="open => !open && (resultSets = [])">
        <DialogScrollContent class="overflow-hidden p-0 shadow-lg self-start my-[10vh]">
            <Command ignore-filter :reset-search-term-on-blur="false">
                <CommandInput
                    v-model="searchTerm"
                    @update:model-value="debouncedSearch"
                    @keyup.esc="searchTerm = ''; resultSets = []"
                    :placeholder="config('sharp.search.placeholder') ?? __('sharp::menu.global_search.default_placeholder')"
                />
                <template v-if="resultSets.length">
                    <CommandList>
                        <template v-for="resultSet in resultSets?.filter(set =>
                            set.resultLinks.length
                            || !set.hideWhenEmpty
                            || set.validationErrors?.length
                        )"
                            :key="resultSet.label"
                        >
                            <CommandGroup :heading="resultSet.label">
                                <template v-if="resultSet.validationErrors?.length">
                                    <ul class="px-2">
                                        <template v-for="error in resultSet.validationErrors">
                                            <li class="text-xs text-destructive">
                                                {{ error }}
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                                <template v-else-if="resultSet.resultLinks.length">
                                    <template v-for="resultLink in resultSet.resultLinks" :key="resultLink.link">
                                        <CommandItem :value="resultLink.link" as-child>
                                            <Link :href="resultLink.link">
                                                <template v-if="resultSet.icon">
                                                    <Icon :icon="resultSet.icon" />
                                                </template>
                                                <div>
                                                    <div>
                                                        {{ resultLink.label }}
                                                    </div>
                                                    <template v-if="resultLink.detail">
                                                        <div class="text-xs text-muted-foreground">
                                                            {{ resultLink.detail }}
                                                        </div>
                                                    </template>
                                                </div>
                                            </Link>
                                        </CommandItem>
                                    </template>
                                </template>
                                <template v-else>
                                    <div class="px-2 py-1.5 text-sm">
                                        {{ resultSet.emptyStateLabel || __('sharp::entity_list.empty_text') }}
                                    </div>
                                </template>
                            </CommandGroup>
                        </template>
                    </CommandList>
                </template>
            </Command>
        </DialogScrollContent>
    </Dialog>
</template>

