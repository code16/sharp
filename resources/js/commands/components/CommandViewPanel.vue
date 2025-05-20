<script setup lang="ts">
    import { CommandManager } from "../CommandManager";
    import { Sheet, SheetContent } from "@/components/ui/sheet";
    import { DialogClose } from "reka-ui";
    import { X } from "lucide-vue-next";
    import { __ } from "@/utils/i18n";

    defineProps<{
        commands: CommandManager,
    }>();
</script>

<template>
    <Sheet
        :open="commands.state.currentCommandResponse?.action === 'view'"
        @update:open="(open) => !open && $nextTick(() => commands.finish())"
    >
        <SheetContent class="w-[calc(100%-2.5rem)] sm:w-3/4 sm:max-w-none" side="left">
            <template v-if="commands.state.currentCommandResponse?.action === 'view'">
                <iframe
                    class="absolute inset-0 size-full border-0"
                    :srcdoc="commands.state.currentCommandResponse.html"
                    sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-modals allow-downloads"
                ></iframe>
            </template>
            <template #close>
                <DialogClose
                    class="absolute right-0 translate-x-full text-white top-0 p-2 md:p-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-secondary"
                    :aria-label="__('sharp::modals.close.aria_label')"
                >
                    <X class="size-6" />
                </DialogClose>
            </template>
        </SheetContent>
    </Sheet>
</template>
