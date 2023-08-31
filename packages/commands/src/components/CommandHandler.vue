<script setup lang="ts">
    import { CommandManager } from "../CommandManager";
    import CommandViewPanel from "./CommandViewPanel.vue";
    import { ref } from "vue";
    import CommandFormModal from "./CommandFormModal.vue";
    import { showAlert } from "@/utils/dialogs";
    import { __ } from "@/utils/i18n";
    import { router } from "@inertiajs/vue3";
    import { CommandReturnData } from "@/types";

    const props = defineProps<{
        commands: CommandManager,
    }>();

    const viewContent = ref(null);

    const handlers = {
        view(data) {
            viewContent.value = data.html;
        },
        info({ message }) {
            showAlert(message, {
                title: __('sharp::modals.command.info.title'),
            });
        },
        link({ link }) {
            const url = new URL(link);
            if(url.origin === location.origin) {
                router.visit(url.pathname + url.search);
            } else {
                location.href = link;
            }
        },
        reload() {
            router.reload();
        },
    };

    props.commands.on('commandReturn', (data: CommandReturnData) => {
        handlers[data.action]?.(data);
    });

    const currentCommand = ref();
    const currentCommandForm = ref();

    props.commands.on('showForm', (command, form) => {
        currentCommand.value = command;
        currentCommandForm.value = form;
    });
</script>

<template>
    <CommandViewPanel
        :content="viewContent"
        @close="viewContent = null"
    />
    <CommandFormModal
        :command="currentCommand"
        :form="currentCommandForm"
        @close="currentCommand = null"
    />
</template>
