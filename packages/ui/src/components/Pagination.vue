<script setup lang="ts">
    import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/20/solid";
    import { PaginatorMetaData } from "@/types";

    const props = defineProps<{
        paginator: { meta: PaginatorMetaData },
        linksOpenable?: boolean
    }>();

    const emit = defineEmits('change');

    function onLinkClick(e) {
        if(props.linksOpenable && (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey)) {
            return;
        }
        emit('change', new URL(e.target.closest('a').href).searchParams.get('page'));
        e.preventDefault();
    }
</script>

<template>
    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
        <template v-if="paginator.meta.prev_page_url">
            <a :href="paginator.meta.prev_page_url"
                class="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus-visible:z-20"
                @click="onLinkClick"
            >
                <ChevronLeftIcon class="h-5 w-5" />
                <span class="sr-only">Précédent</span>
            </a>
        </template>
        <template v-else>
            <span class="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500">
                <ChevronLeftIcon class="h-5 w-5" />
            </span>
        </template>

        <template v-for="link in paginator.meta.links?.slice(1, -1)">
            <template v-if="link.url">
                <a :href="link.url" :aria-current="link.active ? 'page' : null"
                    :class="link.active
                        ? 'relative z-10 inline-flex items-center border border-primary-500 bg-primary-50 px-4 py-2 text-sm font-medium text-primary-600 focus-visible:z-20'
                        : 'relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus-visible:z-20'
                    "
                    @click="onLinkClick"
                >
                    {{ link.label }}
                </a>
            </template>
            <template v-else>
                <span class="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700">
                    {{ link.label }}
                </span>
            </template>
        </template>

        <template v-if="paginator.meta.next_page_url">
            <a :href="paginator.meta.next_page_url"
                class="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus-visible:z-20"
                @click.prevent="onLinkClick"
            >
                <span class="sr-only">Suivant</span>
                <ChevronRightIcon class="h-5 w-5" />
            </a>
        </template>
        <template v-else>
            <span class="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500">
                <ChevronRightIcon class="h-5 w-5" />
            </span>
        </template>
    </nav>
</template>
