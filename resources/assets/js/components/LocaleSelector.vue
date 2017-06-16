<template>
    <div>TODO Locale selector</div>
    <!--<b-dropdown class="SharpLocaleSelector">-->
        <!--<span slot="text" class="SharpLocaleSelector__current">-->
            <!--{{value}}-->
        <!--</span>-->
        <!--<b-dropdown-header>Choisissez une langue</b-dropdown-header>-->
        <!--<b-dropdown-item v-for="locale in filteredLocales"-->
                         <!--class="SharpLocaleSelector__item" :class="{'SharpLocaleSelector__item&#45;&#45;selected':value===locale}"-->
                         <!--@click="$emit('input',locale)"-->
                         <!--v-text="locale" :key="locale">-->
        <!--</b-dropdown-item>-->
    <!--</b-dropdown>-->
</template>

<script>
    export default {
        name: 'SharpLocaleSelector',
        components: {
        },
        props: {
            locales: Array,
            value: String
        },
        computed: {
            filteredLocales() {
                return this.locales;
            },
            currentLocaleIndex() {
                return this.locales.indexOf(this.value);
            }
        }
    }
</script>