<script setup>
import ActionView from "../components/ActionView.vue";
import { GlobalSearch } from 'sharp-search';

const userDropdownTemplate = document.querySelector('#user-dropdown').innerHTML;
</script>


<template>
    <ActionView>
        <template v-slot:search>
            <GlobalSearch :placeholder="$page.props.config.search.placeholder" />
        </template>
        <template v-slot:user-dropdown>
            <component :is="{ template: userDropdownTemplate }" />
        </template>
        <slot />
    </ActionView>
</template>
