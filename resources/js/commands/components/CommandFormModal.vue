<script setup lang="ts">
    import { CommandManager } from "../CommandManager";
    import { ref, watchEffect } from "vue";
    import type SharpForm from '@/form/components/Form.vue';
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader,
        DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import { Button } from "@/components/ui/button";
    import { Loader2 } from "lucide-vue-next";
    import { __ } from "@/utils/i18n";

    const props = defineProps<{
        commands: CommandManager,
    }>();

    const form = ref<InstanceType<typeof SharpForm>>();
    const modalOpen = ref(false);

    watchEffect(() => {
        modalOpen.value = !!props.commands.state.currentCommandForm;
    });
</script>

<template>
    <Dialog
        v-model:open="modalOpen"
        @update:open="!$event && $nextTick(() => commands.finish())"
    >
        <DialogScrollContent class="sm:max-w-[558px]" @pointer-down-outside.prevent>
            <template v-if="commands.state.currentCommandForm">
                <DialogHeader>
                    <DialogTitle>
                        {{ commands.state.currentCommand.modal_title ?? commands.state.currentCommand.label }}
                    </DialogTitle>
                </DialogHeader>
                <div>
                    <SharpForm
                        :post-fn="(data) => commands.postForm(data)"
                        :entity-key="commands.state.currentCommandEndpoints.entityKey"
                        :instance-id="commands.state.currentCommandEndpoints.instanceId"
                        :form="commands.state.currentCommandForm"
                        @loading="(loading) => commands.state.currentCommandFormLoading = loading"
                        ref="form"
                    />
                </div>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">
                            {{ __('sharp::modals.cancel_button') }}
                        </Button>
                    </DialogClose>
                    <Button :disabled="commands.state.currentCommandFormLoading" @click="form.submit()">
                        <template v-if="commands.state.currentCommandFormLoading">
                            <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                        </template>
                        {{ commands.state.currentCommand.modal_confirm_label ?? __('sharp::modals.ok_button') }}
                    </Button>
                </DialogFooter>
            </template>
        </DialogScrollContent>
    </Dialog>
</template>
