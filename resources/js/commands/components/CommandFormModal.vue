<script setup lang="ts">
    import { CommandManager } from "../CommandManager";
    import { ref, watchEffect } from "vue";
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
                        {{ commands.state.currentCommandForm.config.title  }}
                    </DialogTitle>
                    <template v-if="commands.state.currentCommandForm.config.description">
                        <DialogDescription>
                            {{ commands.state.currentCommandForm.config.description }}
                        </DialogDescription>
                    </template>
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
                        {{ commands.state.currentCommandForm.config.buttonLabel ?? __('sharp::modals.command.submit_button') }}
                    </Button>
                </DialogFooter>
            </template>
        </DialogScrollContent>
    </Dialog>
</template>
