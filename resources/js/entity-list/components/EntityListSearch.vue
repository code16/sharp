<script setup lang="ts">
    import { EntityListData } from "@/types";
    import { __ } from "@/utils/i18n";
    import { Search, X } from "lucide-vue-next";
    import { useCurrentElement, useFocusWithin, useMousePressed } from "@vueuse/core";
    import { ref, watch } from "vue";
    import { Input } from "@/components/ui/input";
    import { Button } from "@/components/ui/button";
    import { cn } from "@/utils/cn";

    const props = defineProps<{
        entityList: EntityListData,
        expanded: boolean,
        inline?: boolean,
        disabled?: boolean,
    }>();

    const emit = defineEmits(['submit', 'update:expanded']);

    const el = ref<HTMLElement>();
    const input = ref<HTMLInputElement>();
    const { focused: focusedWithin } = useFocusWithin(el);
    const { pressed } = useMousePressed({ target: el });
    const search = ref(props.entityList.query?.search ?? '');
    let savedSearchBeforeBlur = '';

    watch(focusedWithin, () => {
        if(focusedWithin.value) {
            if(savedSearchBeforeBlur) {
                search.value = savedSearchBeforeBlur;
                savedSearchBeforeBlur = '';
            }
            if(pressed.value) {
                watch(pressed, () => {
                    if(!pressed.value) {
                        emit('update:expanded', true);
                    }
                }, { once: true });
            } else {
                emit('update:expanded', true);
            }
        } else if(!pressed.value) {
            // in the case of the focus is lost even if user has pressed,
            // we don't hide the button (safari lose focus on button click)
            savedSearchBeforeBlur = !props.entityList.query?.search ? search.value : '';
            search.value = props.entityList.query?.search;
            emit('update:expanded', false);
        }
    });

    watch(() => props.entityList, () => {
        search.value = props.entityList.query?.search ?? '';
        emit('update:expanded', false);
    });

    function onSubmit() {
        emit('update:expanded', false);
        emit('submit', search.value);
    }

    function onInput() {
        if(!props.expanded) {
            emit('update:expanded', true);
        }
    }
</script>

<template>
    <form @submit.prevent="onSubmit" ref="el">
        <div class="relative z-[1]" :class="[inline ? 'h-8 w-[150px] @5xl/root-card:w-[200px]' : '']">
            <div class="top-0 left-0 group flex gap-3" :class="cn(
                inline ? 'absolute h-8' : 'flex-col',
                inline && expanded ? '-mr-[100px]' : ''
            )">
                <div class="relative" :class="[inline ? '' : 'w-full']">
                    <Input
                        :placeholder="__('sharp::action_bar.list.search.placeholder')"
                        v-model="search"
                        :class="cn('w-full pl-8 h-9', {
                            'h-8 w-[150px] @5xl/root-card:w-[200px]': inline,
                            'sm:!w-[300px]': inline && expanded,
                            'pr-8': props.entityList.query?.search,
                        })"
                        :disabled="disabled"
                        type="search"
                        ref="input"
                        @input="onInput"
                    />
                    <Search class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none" />
                    <template v-if="props.entityList.query?.search">
                        <Button type="button" class="absolute right-0 top-0 h-full" size="sm" variant="ghost" @click="$emit('submit', null)">
                            <X class="h-4 w-4 text-muted-foreground" />
                        </Button>
                    </template>
                </div>
                <Button type="submit"
                    :class="cn('h-8 hidden', expanded ? 'inline-flex' : '')"
                    size="sm"
                >
                    {{ __('sharp::action_bar.list.search.button') }}
                </Button>
            </div>
        </div>
    </form>
</template>
