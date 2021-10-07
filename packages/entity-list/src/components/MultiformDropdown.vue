<template>
    <Dropdown v-bind="$attrs" :text="l('action_bar.list.forms_dropdown')">
        <template v-for="form in forms">
            <DropdownItem @click="handleSelected(form)" :key="form.key">
                <div class="row gx-2">
                    <template v-if="hasIcon">
                        <div class="col-auto">
                            <div class="fa-fw">
                                <ItemVisual :item="form" />
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
    import { Dropdown, DropdownItem, ItemVisual } from "sharp-ui";

    export default {
        components: {
            Dropdown,
            DropdownItem,
            ItemVisual,
        },
        props: {
            forms: Array,
        },
        methods: {
            l: lang,
            handleSelected(form) {
                this.$emit('select', form);
            }
        },
        computed: {
            hasIcon() {
                return this.forms?.some(form => form.icon || form.image);
            },
        },
    }
</script>
