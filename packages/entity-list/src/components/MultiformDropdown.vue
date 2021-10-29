<template>
    <Dropdown v-bind="$attrs" :text="l('action_bar.list.forms_dropdown')">
        <template v-for="form in visibleForms">
            <DropdownItem @click="handleSelected(form)" :key="form.key">
                <div class="row gx-2">
                    <template v-if="hasIcon">
                        <div class="col-auto">
                            <div class="fa-fw">
                                <i class="fa" :class="form.icon"></i>
                            </div>
                        </div>
                    </template>
                    <div class="col" style="min-width: 0">
                        {{ form.label }}
                    </div>
                </div>
            </DropdownItem>
        </template>
    </Dropdown>
</template>

<script>
    import { lang } from "sharp";
    import { Dropdown, DropdownItem } from "sharp-ui";

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
            l: lang,
            handleSelected(form) {
                this.$emit('select', form);
            }
        },
    }
</script>
