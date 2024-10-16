<script setup lang="ts">
    import { Modal } from "@/components/ui";
    import { Form } from "@/form/Form";
    import { ref } from "vue";
    import { FormData } from "@/types";
    import { useParentForm } from "@/form/useParentForm";

    const emit = defineEmits(['cancel']);

    const props = defineProps<{
        form?: Form,
        visible: boolean,
        post: (data: FormData['data']) => Promise<void>,
    }>()

    const parentForm = useParentForm();
    const formComponent = ref<InstanceType<typeof FormComponent>>();
    const loading = ref(false);

    function submit(e) {
        e.preventDefault();
        loading.value = true;
        formComponent.value.submit()
            .finally(() => {
                loading.value = false;
            });
    }
</script>

<template>
    <Modal
        :visible="visible"
        :loading="loading"
        @ok="submit"
        @close="$emit('cancel')"
        @cancel="$emit('cancel')"
    >
        <template v-slot:title>
            <slot name="title"></slot>
        </template>

        <template v-if="form">
            <SharpForm
                :entity-key="parentForm.entityKey"
                :instance-id="parentForm.instanceId"
                :form="form"
                :post-fn="post"
                inline
                style="transition-duration: 300ms"
                ref="formComponent"
            />
        </template>
    </Modal>
</template>
