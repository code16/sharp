<script setup lang="ts">
    import { Dialog, DialogScrollContent } from "@/components/ui/dialog";
    import { cn } from "@/utils/cn";
    import { DialogContent, DialogOverlay, DialogPortal } from "reka-ui";

    const isFullscreen = defineModel<boolean>('fullscreen');

    function onBackdropPointerDown(pointerDownEvent: PointerEvent) {
        window.addEventListener('pointerup', (pointerUpEvent: PointerEvent) => {
            if(pointerDownEvent.target === pointerUpEvent.target) {
                isFullscreen.value = false;
            }
        }, { once: true });
    }
</script>

<template>
    <template v-if="isFullscreen">
        <Dialog v-model:open="isFullscreen">
            <DialogPortal>
                <DialogOverlay
                    class="fixed inset-0 z-50  grid grid-cols-1 grid-rows-1 justify-items-center py-4 sm:px-4 overflow-y-auto bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
                >
                    <DialogContent
                        :class="
                            cn(
                              'relative z-50 grid grid-cols-1 grid-rows-1 w-full max-w-7xl gap-4 border bg-background shadow-lg duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 sm:rounded-lg',
                            )"
                        @pointer-down-outside.prevent="onBackdropPointerDown($event.detail.originalEvent)"
                        >
                        <slot />
                    </DialogContent>
                </DialogOverlay>
            </DialogPortal>
        </Dialog>
    </template>
    <template v-else>
        <slot />
    </template>
</template>
