<script setup lang="ts">
    import { Modal } from '@/components/ui';
    import Form from '@/form/components/Form.vue';
    import { CommandManager } from "../CommandManager";
    import { ref } from "vue";

    defineProps<{
        commands: CommandManager,
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
                    @loading="(loading) => commands.state.currentCommandFormLoading = loading"
                    style="transition-duration: 300ms"
                    ref="form"
                />
            </template>
        </transition>
    </Modal>
</template>
