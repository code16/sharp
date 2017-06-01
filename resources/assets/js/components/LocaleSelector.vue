<template>
    <b-dropdown class="SharpLocaleSelector">
        <span slot="text" class="SharpLocaleSelector__current">
            {{value}}
        </span>
        <b-dropdown-header>Choisissez une langue</b-dropdown-header>
        <b-dropdown-item v-for="locale in filteredLocales"
                         class="SharpLocaleSelector__item" :class="{'SharpLocaleSelector__item--selected':value===locale}"
                         @click="$emit('input',locale)"
                         v-text="locale" :key="locale">
        </b-dropdown-item>
    </b-dropdown>
</template>

<script>
    import bDropdown from './vendor/bootstrap-vue/components/dropdown';
    import bDropdownItem from './vendor/bootstrap-vue/components/dropdown-item';
    import bDropdownHeader from './vendor/bootstrap-vue/components/dropdown-header';

    export default {
        name: 'SharpLocaleSelector',
        components: {
            bDropdown:{
                extends:bDropdown,
                methods:{
                    toggle() { !this.disabled && (this.visible = !this.visible) }
                }
            },
            bDropdownItem, bDropdownHeader
        },
        props: {
            locales: Array,
            value: String
        },
        computed: {
            filteredLocales() {
                return this.locales;//.filter(l => l!==this.value);
            }
        }
    }
</script>