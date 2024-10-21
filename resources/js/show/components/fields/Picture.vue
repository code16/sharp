<script setup lang="ts">
    import FieldLayout from "../FieldLayout.vue";
    import { ShowPictureFieldData } from "@/types";
    import { ShowFieldProps } from "../../types";

    defineProps<ShowFieldProps<ShowPictureFieldData>>();
</script>

<template>
    <FieldLayout>
        <div class="flex" :class="[isRightCol ? 'md:justify-end' : '']">
            <img class="rounded-md" :src="value" alt="">
        </div>
    </FieldLayout>
</template>
