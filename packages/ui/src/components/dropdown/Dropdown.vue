<script setup lang="ts">
    import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
    import { ChevronDownIcon } from '@heroicons/vue/20/solid';
    import { ButtonProps } from "../types";
    import { ref } from "vue";
    import Button from "../Button.vue";

    withDefaults(defineProps<ButtonProps & {
        showCaret?: boolean,
        title?: string,
        toggleClass?: string, // needed ?
    }>(), {
        showCaret: true,
    });

    const toggle = ref<HTMLButtonElement | null>();

    defineExpose({
        open() {
            toggle.value.click();
        },
        close() {
            document.body.click();
        }
    });
</script>

<template>
    <Menu as="div" class="relative inline-block text-left" v-slot="{ close }">
        <div>
            <MenuButton as="template">
                <Button :class="toggleClass" :variant="variant" :title="title" v-bind="$props">
                    <slot name="text" />
                    <template v-if="showCaret">
                        <ChevronDownIcon class="-mr-1 h-5 w-5 text-gray-400" aria-hidden="true" />
                    </template>
                </Button>
            </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems class="absolute left-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div class="py-1">
                    <slot :hide="close" />
                </div>
            </MenuItems>
        </transition>
    </Menu>
<!--    <b-dropdown-->
<!--        class="SharpDropdown"-->
<!--        :toggle-class="toggleClass"-->
<!--        :disabled="disabled"-->
<!--        variant="custom"-->
<!--        no-flip-->
<!--        v-bind="$attrs"-->
<!--        v-on="$listeners"-->
<!--        ref="dropdown"-->
<!--    >-->
<!--        <template v-slot:button-content>-->
<!--            <slot name="text" />-->
<!--        </template>-->

<!--        <slot :hide="hide" />-->
<!--    </b-dropdown>-->
</template>
