<script setup lang="ts">
    import { EntityListQueryParamsData } from "@/types";
    import { __ } from "@/utils/i18n";
    import { Search, X, CornerDownLeft } from "lucide-vue-next";
    import { useFocusWithin, useMousePressed } from "@vueuse/core";
    import { ref, watch } from "vue";
    import { Input } from "@/components/ui/input";
    import { Button } from "@/components/ui/button";
    import { cn } from "@/utils/cn";

    const props = defineProps<{
        query: EntityListQueryParamsData,
        reordering: boolean,
        focused: boolean,
    }>();

    const emit = defineEmits(['submit', 'update:focused']);

    const el = ref<HTMLDivElement>();
    const input = ref<HTMLInputElement>();
    const { focused } = useFocusWithin(el as any);
    const { pressed } = useMousePressed({ target: el as any });
    const search = ref(props.query.search);
    const showButton = ref(false);
    let savedSearch = '';

    watch(focused, () => {
        if(focused.value) {
            if(savedSearch) {
                search.value = savedSearch;
                savedSearch = '';
            }
            if(pressed.value) {
                watch(pressed, () => {
                    if(!pressed.value) {
                        emit('update:focused', true);
                        showButton.value = true;
                    }
                }, { once: true });
            } else {
                showButton.value = true;
                emit('update:focused', true);
            }
        } else {
            showButton.value = false;
            savedSearch = !props.query.search ? search.value : '';
            search.value = props.query.search;
            emit('update:focused', false);
        }
    });

    watch(() => props.query.search, v => search.value = v);
</script>

<template>
    <form @submit.prevent="$emit('submit', search)">
        <div class="group flex gap-3" ref="el">
            <div class="relative">
                <Input
                    :placeholder="__('sharp::action_bar.list.search.placeholder')"
                    v-model="search"
                    :disabled="reordering"
                    :class="cn('w-[150px] lg:w-[200px] px-8 h-8', showButton  ? 'lg:w-[300px]' : '')"
                    type="search"
                    ref="input"
                />
                <Search class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none" />
                <template v-if="query.search">
                    <Button type="button" class="absolute right-0 top-0 h-8" size="sm" variant="ghost" @click="$emit('submit', null)">
                        <X class="h-4 w-4 text-muted-foreground" />
                    </Button>
                </template>
            </div>
            <Button type="submit" :class="cn('h-8 hidden', showButton ? 'inline-flex' : '')" size="sm">
                {{ __('sharp::action_bar.list.search.button') }}
            </Button>
        </div>
    </form>

</template>
