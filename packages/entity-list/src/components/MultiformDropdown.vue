<template>
    <Dropdown v-bind="$attrs" :text="l('action_bar.list.forms_dropdown')">
        <template v-for="(form,key) in forms">
            <DropdownItem @click="handleSelected(form)" :key="key">
                <div class="row gx-2">
                    <div class="col-auto">
                        <ItemVisual :item="form" icon-class="fa-fw"/>
                    </div>
                    <div class="col">
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
        }
    }
</script>
