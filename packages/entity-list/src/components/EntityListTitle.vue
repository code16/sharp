<script setup lang="ts">
    import { __ } from "@/utils/i18n";

    defineProps<{
        count: number,
        search: string,
    }>();
</script>

<template>
    <div class="SharpEntityListTitle">
        <div class="row align-items-center gx-2 h-100">
            <template v-if="$slots.default">
                <div class="col-auto">
                    <slot />
                </div>
            </template>
            <template v-if="count">
                <div class="col-auto">
                    <div class="SharpEntityListTitle__info">
                        <div class="row" style="--bs-gutter-x: inherit">
                            <template v-if="$slots.default">
                                <div class="col-auto">
                                    &bull;
                                </div>
                            </template>
                            <div class="col">
                                <span class="text-nowrap">{{ __('sharp::action_bar.list.items_count', { count }) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-if="search">
                <div class="col-auto">
                    <div class="SharpEntityListTitle__info">
                        <div class="row" style="--bs-gutter-x: inherit">
                            <div class="col-auto">
                                &bull;
                            </div>
                            <div class="col">
                                <span class="text-nowrap">
                                    {{ __('sharp::action_bar.list.search.title', { search }) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
