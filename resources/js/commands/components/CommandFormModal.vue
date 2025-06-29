<script setup lang="ts">
    import { CommandManager } from "../CommandManager";
    import { ref, useTemplateRef, watchEffect } from "vue";
    import type SharpForm from '@/form/components/Form.vue';
    import {
        Dialog,
        DialogClose,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import { Button } from "@/components/ui/button";
    import { __ } from "@/utils/i18n";
    import { CommandFormExtraData } from "@/commands/types";
    import { useEventListener } from "@vueuse/core";
    import { FormEvents } from "@/form/Form";

    const props = defineProps<{
        commands: CommandManager,
    }>();

    const form = ref<InstanceType<typeof SharpForm>>();
    const modalOpen = ref(false);
    const currentFormUpdatedKey = ref(0);

    const content = useTemplateRef<InstanceType<typeof DialogScrollContent>>('content');

    useEventListener<FormEvents>(() => props.commands.state.currentCommandForm, 'error', () => {
        content.value.scrollToTop();
    });

    watchEffect(() => {
        modalOpen.value = !!props.commands.state.currentCommandForm;
        currentFormUpdatedKey.value++;
    });
</script>

<template>
    <Dialog
        v-model:open="modalOpen"
        @update:open="!$event && $nextTick(() => commands.finish())"
    >
        <DialogScrollContent
            class="sm:max-w-[558px] gap-8"
            @pointer-down-outside.prevent
            ref="content"
        >
            <template v-if="commands.state.currentCommandForm">
                <DialogHeader>
                    <DialogTitle>
                        {{ commands.state.currentCommandForm.config.title  }}
                    </DialogTitle>
                    <template v-if="commands.state.currentCommandForm.config.description">
                        <DialogDescription as="div" v-html="commands.state.currentCommandForm.config.description">
                        </DialogDescription>
                    </template>
                </DialogHeader>
                <div>
                    <SharpForm
                        :post-fn="(data) => commands.postForm(data)"
                        :form="commands.state.currentCommandForm"
                        :show-error-alert="commands.state.currentCommandForm.hasErrors"
                        :error-alert-message="commands.state.currentCommandForm.fieldError('error')"
                        @loading="(loading) => commands.state.currentCommandFormLoading = loading"
                        :key="`form-${currentFormUpdatedKey}`"
                        modal
                        ref="form"
                    />
                </div>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">
                            {{ __('sharp::modals.cancel_button') }}
                        </Button>
                    </DialogClose>
                    <template v-if="commands.state.currentCommandForm.config.showSubmitAndReopenButton">
                        <Button variant="outline"
                            :disabled="commands.state.currentCommandFormLoading"
                            @click="form.submit<CommandFormExtraData>({ _shouldReopen: true })"
                        >
                            {{ commands.state.currentCommandForm.config.submitAndReopenButtonLabel ?? __('sharp::modals.command.submit_and_reopen_button') }}
                        </Button>
                    </template>
                    <Button :disabled="commands.state.currentCommandFormLoading" @click="form.submit()">
                        {{ commands.state.currentCommandForm.config.buttonLabel ?? __('sharp::modals.command.submit_button') }}
                    </Button>
                </DialogFooter>
            </template>
        </DialogScrollContent>
    </Dialog>
</template>
