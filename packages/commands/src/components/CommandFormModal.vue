<script setup lang="ts">
    import { Modal } from '@sharp/ui';
    import { Form } from '@sharp/form';
    import { CommandManager } from "../CommandManager";
    import { ref } from "vue";

    defineProps<{
        commands: CommandManager,
        entityKey: string,
        instanceId: string | number,
    }>();

    const form = ref<InstanceType<typeof Form>>();
</script>

<template>
    <Modal
        modal-class="form-modal"
        :visible="!!commands.state.currentCommandForm || commands.state.currentCommandFormLoading"
        :loading="commands.state.currentCommandFormLoading"
        :title="commands.state.currentCommand?.modal_title ?? commands.state.currentCommand?.label"
        :ok-title="commands.state.currentCommand?.modal_confirm_label"
        @ok="form.submit()"
        @hidden="commands.finish()"
    >
        <transition>
            <template v-if="!!commands.state.currentCommandForm">
                <Form
                    :post-fn="commands.postForm"
                    :entity-key="commands.state.currentCommandEndpoints.entityKey"
                    :instance-id="commands.state.currentCommandEndpoints.instanceId"
                    :form="commands.state.currentCommandForm"
                    :show-alert="false"
                    independant
                    ignore-authorizations
                    @loading="commands.state.currentCommandFormLoading = $event"
                    style="transition-duration: 300ms"
                    ref="form"
                />
            </template>
        </transition>
    </Modal>
</template>
