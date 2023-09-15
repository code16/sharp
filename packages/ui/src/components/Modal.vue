<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ref } from 'vue'
    import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import { XMarkIcon } from "@heroicons/vue/24/outline";
    import Button from "./Button.vue";

    defineProps<{
        visible?: boolean,
        cancelTitle?: string,
        okVariant?: string,
        okOnly?: boolean,
        okTitle?: string,
        noCloseOnBackdrop?: boolean,
        isError?: boolean,
        loading?: boolean,
    }>();

    const disableNextClose = ref(false);

    const emit = defineEmits(['ok', 'cancel', 'close', 'show', 'hidden', 'shown', 'update:visible']);

    function close(eventName?: 'ok' | 'cancel' | 'close') {
        if(disableNextClose.value) {
            disableNextClose.value = false;
            return;
        }

        if(eventName) {
            const event = new CustomEvent(eventName);
            emit(eventName, event);
            if (event.defaultPrevented) {
                return;
            }
        }

        emit('update:visible', false);
    }
</script>

<template>
    <TransitionRoot
        as="template"
        @before-enter="$emit('show')"
        @after-enter="$emit('shown')"
        @after-leave="$emit('hidden')"
        :show="!!visible"
    >
        <Dialog as="div" class="relative z-50" @close="close">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <template v-if="noCloseOnBackdrop">
                        <div class="absolute inset-0" @mousedown="disableNextClose = true" @touchstart="disableNextClose = true"></div>
                    </template>
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-lg sm:p-6"
                        >
                            <div>
                                <template v-if="$slots.title">
                                    <DialogTitle as="h3" class="text-lg font-medium leading-6 mb-6"
                                        :class="isError ? 'text-red-700' : 'text-gray-900'"
                                    >
                                        <slot name="title" />
                                    </DialogTitle>
                                </template>

                                <transition enter-active-class="duration-300" leave-active-class="duration-200">
                                    <div>
                                        <slot :close="close" />
                                    </div>
                                </transition>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <Button outline>
                                    {{ cancelTitle ?? __('sharp::modals.cancel_button') }}
                                </Button>
                                <Button class="min-w-[70px]" :disabled="loading" :variant="okVariant" @click="close('ok')">
                                    {{ okTitle ?? __('sharp::modals.ok_button') }}
                                </Button>
                            </div>
                            <template v-if="!okOnly">
                                <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                    <button type="button" class="rounded-md bg-white text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" @click="close">
                                        <span class="sr-only">Close</span>
                                        <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                    </button>
                                </div>
                            </template>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
