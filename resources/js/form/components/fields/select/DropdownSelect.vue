<script setup lang="ts">
    import { FormFieldProps } from "@/form/types";
    import { FormSelectFieldData } from "@/types";

    defineProps<FormFieldProps<FormSelectFieldData>>();
</script>

<template>
<!--    <Multiselect-->
<!--        :id="id"-->
<!--        :value="value"-->
<!--        :searchable="false"-->
<!--        :options="multiselectOptions"-->
<!--        :multiple="multiple"-->
<!--        :hide-selected="multiple"-->
<!--        :close-on-select="!multiple"-->
<!--        :custom-label="multiselectLabel"-->
<!--        :placeholder="placeholder"-->
<!--        :disabled="readOnly"-->
<!--        :max="maxSelected"-->
<!--        :allow-empty="clearable"-->
<!--        @input="handleInput"-->
<!--        @open="$emit('open')"-->
<!--        @close="$emit('close')"-->
<!--        ref="multiselect"-->
<!--    >-->
<!--        <template v-if="clearable && !multiple && value != null" v-slot:caret>-->
<!--            <ClearButton class="SharpSelect__clear-button" @click="$emit('input', null)" />-->
<!--        </template>-->
<!--        <template v-slot:tag="{ option, remove }" :key="option">-->
<!--            <span class="multiselect__tag">-->
<!--                <span>{{ multiselectLabel(option) }}</span>-->
<!--                <i aria-hidden="true" tabindex="1" @keypress.enter.prevent="remove(option)" @mousedown.prevent.stop="remove(option)" class="multiselect__tag-icon"></i>-->
<!--            </span>-->
<!--        </template>-->
<!--        <template v-slot:option>-->
<!--            <slot name="option" />-->
<!--        </template>-->
<!--    </Multiselect>-->
</template>
