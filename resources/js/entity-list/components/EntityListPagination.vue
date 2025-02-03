<script setup lang="ts">
    import { EntityListData } from "@/types";
    import { __ } from "@/utils/i18n";
    import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from "lucide-vue-next";
    import { Button } from "@/components/ui/button";
    import en from "apexcharts/dist/locales/en.json";
    import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

    const props = defineProps<{
        entityList: EntityListData,
        linksOpenable?: boolean
    }>();

    const emit = defineEmits(['change']);

    function onLinkClick(e: MouseEvent, inSelect = false) {
        if(props.linksOpenable && (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey)) {
            e.stopPropagation();
            return;
        }
        const href = (e.target as HTMLElement).closest('a').href;
        if(href && !inSelect) {
            emit('change', new URL(href).searchParams.get('page'));
            e.preventDefault();
        }
    }

    function linkUrl(page: number) {
        const url = new URL(props.entityList.meta.first_page_url);
        url.searchParams.set('page', String(page));
        return url.toString();
    }
</script>

<template>
    <nav class="flex justify-center w-full gap-x-6 lg:gap-x-8" aria-label="Pagination">
        <div class="flex gap-2">
            <Button
                :as="entityList.meta.prev_page_url ? 'a' : 'button'"
                class="hidden sm:flex"
                size="icon"
                variant="outline"
                :href="entityList.meta.first_page_url"
                :disabled="!entityList.meta.prev_page_url"
                @click="onLinkClick"
            >
                <ChevronsLeft class="h-4 w-4"/>
                <span class="sr-only">
                    {{ __('sharp::entity_list.pagination.first') }}
                </span>
            </Button>
            <Button
                :as="entityList.meta.prev_page_url ? 'a' : 'button'"
                size="icon"
                variant="outline"
                :href="entityList.meta.prev_page_url"
                :disabled="!entityList.meta.prev_page_url"
                @click="onLinkClick"
            >
                <ChevronLeft class="h-4 w-4"/>
                <span class="sr-only">
                    {{ __('sharp::entity_list.pagination.previous') }}
                </span>
            </Button>
        </div>
        <div class="flex min-w-[100px] gap-2 items-center justify-center text-sm font-medium">
            <template v-if="entityList.meta.last_page">
                <span>
                    {{ __('sharp::entity_list.pagination.current').split(':current_page')[0] }}
                </span>
                <Select :model-value="String(entityList.meta.current_page)" @update:model-value="$emit('change', $event)">
                    <SelectTrigger
                        class="w-10 justify-center [&_svg]:hidden"
                        :aria-label="__('sharp::entity_list.pagination.select.aria_label')"
                    >
                        {{ entityList.meta.current_page }}
                    </SelectTrigger>
                    <SelectContent :align-offset="-22">
                        <template v-for="page in entityList.meta.last_page">
                            <SelectItem :value="String(page)">
                                <template v-if="linksOpenable">
                                    <a class="absolute inset-0" :href="linkUrl(page)"  @pointerup="onLinkClick($event, true)"></a>
                                </template>
                                {{ page }}
                            </SelectItem>
                        </template>
                    </SelectContent>
                </Select>
                <span>
                    {{ __('sharp::entity_list.pagination.current', { last_page: entityList.meta.last_page }).split(':current_page')[1] }}
                </span>
            </template>
            <template v-else>
                {{ __('sharp::entity_list.pagination.current_simple').replace(':current_page', entityList.meta.current_page+'') }}
            </template>
        </div>

        <!--            <template v-for="link in entityList.meta.links?.slice(1, -1)">-->
        <!--                <template v-if="link.url">-->
        <!--                    <Button class="w-8 h-8 p-0" :variant="link.active ? 'default' : 'outline'" size="icon" as-child>-->
        <!--                        <a :href="link.url" @click="onLinkClick">-->
        <!--                            {{ link.label }}-->
        <!--                        </a>-->
        <!--                    </Button>-->
        <!--                </template>-->
        <!--                <template v-else>-->
        <!--                    <div class="w-8 h-8 flex items-center justify-center">-->
        <!--                        {{ link.label }}-->
        <!--                    </div>-->
        <!--                </template>-->
        <!--            </template>-->

        <div class="flex gap-2">
            <Button
                :as="entityList.meta.next_page_url ? 'a' : 'button'"
                size="icon"
                variant="outline"
                :href="entityList.meta.next_page_url"
                :disabled="!entityList.meta.next_page_url"
                @click="onLinkClick"
            >
                <span class="sr-only">
                    {{ __('sharp::entity_list.pagination.next') }}
                </span>
                <ChevronRight class="h-4 w-4"/>
            </Button>
            <Button
                :as="entityList.meta.next_page_url ? 'a' : 'button'"
                class="hidden sm:flex"
                :class="!entityList.meta.last_page_url ? 'invisible' : ''"
                size="icon"
                variant="outline"
                :href="entityList.meta.last_page_url"
                :disabled="!entityList.meta.next_page_url"
                @click="onLinkClick"
            >
                <ChevronsRight class="h-4 w-4"/>
                <span class="sr-only">
                    {{ __('sharp::entity_list.pagination.last') }}
                </span>
            </Button>
        </div>
    </nav>
</template>
