<script setup lang="ts">
    import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
    import { ChevronDownIcon } from '@heroicons/vue/20/solid';
    import { ButtonProps } from "../types";
    import { ref } from "vue";
    import Button from "../../../../js-legacy/components/ui/Button.vue";

    withDefaults(defineProps<ButtonProps & {
        right?: boolean,
        showCaret?: boolean,
        title?: string,
        toggleClass?: any, // needed ?
    }>(), {
        showCaret: true,
    });

    const toggle = ref<InstanceType<typeof MenuButton> | null>();
    const menuItems = ref<InstanceType<typeof MenuItems> | null>();

    defineExpose({
        open() {
            // close other dropdowns
            document.body.dispatchEvent(new MouseEvent('mousedown'));
            document.body.dispatchEvent(new MouseEvent('click'));

            toggle.value.$el.$el.click();
        },
        close() {
            menuItems.value.$el.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        }
    });
</script>

<template>
    <Menu as="div" class="relative inline-block text-left" v-slot="{ close }">
        <div>
            <MenuButton as="template" ref="toggle">
                <Button class="inline-flex" :class="toggleClass" :variant="variant" :title="title" v-bind="$props">
                    <slot name="text" />
                    <template v-if="showCaret">
                        <ChevronDownIcon class="-mr-1 h-5 w-5 text-gray-400" aria-hidden="true" />
                    </template>
                </Button>
            </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95"
            @before-enter="$emit('show')"
            @after-enter="$emit('shown')"
            @before-leave="$emit('hide')"
        >
            <MenuItems class="absolute z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                :class="[right ? 'right-0' : 'left-0']"
                ref="menuItems"
            >
                <div class="py-1">
                    <slot name="prepend"></slot>
                    <slot :hide="close" />
                    <slot name="append"></slot>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
