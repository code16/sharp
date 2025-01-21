<script setup lang="ts">
    import { reactive, ref } from "vue";
    import { ShowLayoutSectionData } from "@/types";

    const props = defineProps<{
        section: ShowLayoutSectionData,
    }>();

    const collapsed = ref(props.section.collapsable);

    const slotProps = reactive({
        collapsed,
        onCollapseToggle() {
            collapsed.value = !collapsed.value;
        },
    })

    defineSlots<{
        default(props: typeof slotProps): any
    }>()
</script>

<template>
    <section>
        <slot v-bind="slotProps" />
    </section>
</template>
