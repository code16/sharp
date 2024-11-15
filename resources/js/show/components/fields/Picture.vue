<script setup lang="ts">
    import ShowFieldLayout from "../ShowFieldLayout.vue";
    import { ShowPictureFieldData } from "@/types";
    import { ShowFieldProps } from "../../types";

    const props = defineProps<ShowFieldProps<ShowPictureFieldData>>();
</script>

<template>
    <ShowFieldLayout v-bind="props">
        <div class="flex" :class="[props.isRightCol ? 'md:justify-end' : '']">
            <img class="rounded-md" :src="props.value" alt="">
        </div>
    </ShowFieldLayout>
</template>
