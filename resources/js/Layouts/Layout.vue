<script setup lang="ts">
import ActionView from "../components/ActionView.vue";
import { GlobalSearch } from '@sharp/search';
import { config } from "../util/config";
import LeftNav from "../components/LeftNav.vue";

const userDropdownTemplate = document.querySelector('#user-dropdown').innerHTML;
</script>


<template>
    <LeftNav />
    <ActionView>
        <template v-slot:search>
            <GlobalSearch :placeholder="config('sharp.search.placeholder')" />
        </template>
        <template v-slot:user-dropdown>
            <component :is="{ template: userDropdownTemplate }" />
        </template>
<!--        <slot />-->
    </ActionView>
</template>
