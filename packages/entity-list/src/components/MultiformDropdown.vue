<script setup lang="ts">
    import { __ } from "@/util/i18n";
</script>

<template>
    <Dropdown v-bind="$attrs" :text="__('sharp::action_bar.list.forms_dropdown')">
        <template v-for="form in visibleForms" :key="form.key">
            <DropdownItem @click="handleSelected(form)">
                <div class="row gx-2 flex-nowrap">
                    <template v-if="hasIcon">
                        <div class="col-auto">
                            <div class="fa-fw">
                                <i class="fa" :class="form.icon"></i>
                            </div>
                        </div>
                    </template>
                    <div class="col">
                        {{ form.label }}
                    </div>
                </div>
            </DropdownItem>
        </template>
    </Dropdown>
</template>

<script lang="ts">
    import { Dropdown, DropdownItem } from "@sharp/ui";

    export default {
        components: {
            Dropdown,
            DropdownItem,
        },
        props: {
            forms: Array,
        },
        computed: {
            hasIcon() {
                return this.forms?.some(form => form.icon);
            },
            visibleForms() {
                return this.forms?.filter(form => !!form.label || form.icon);
            },
        },
        methods: {
            __,
            handleSelected(form) {
                this.$emit('select', form);
            }
        },
    }
</script>
