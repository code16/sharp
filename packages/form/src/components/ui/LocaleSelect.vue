<template>
    <Dropdown class="SharpForm__locale-dropdown" toggle-class="d-flex align-items-center" v-bind="$attrs">
        <template v-slot:text>
            <!-- LanguageIcon -- @heroicons/vue/20 -->
            <svg class="d-block me-2" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M7.75 2.75a.75.75 0 00-1.5 0v1.258a32.987 32.987 0 00-3.599.278.75.75 0 10.198 1.487A31.545 31.545 0 018.7 5.545 19.381 19.381 0 017 9.56a19.418 19.418 0 01-1.002-2.05.75.75 0 00-1.384.577 20.935 20.935 0 001.492 2.91 19.613 19.613 0 01-3.828 4.154.75.75 0 10.945 1.164A21.116 21.116 0 007 12.331c.095.132.192.262.29.391a.75.75 0 001.194-.91c-.204-.266-.4-.538-.59-.815a20.888 20.888 0 002.333-5.332c.31.031.618.068.924.108a.75.75 0 00.198-1.487 32.832 32.832 0 00-3.599-.278V2.75z" />
                <path fill-rule="evenodd" d="M13 8a.75.75 0 01.671.415l4.25 8.5a.75.75 0 11-1.342.67L15.787 16h-5.573l-.793 1.585a.75.75 0 11-1.342-.67l4.25-8.5A.75.75 0 0113 8zm2.037 6.5L13 10.427 10.964 14.5h4.073z" clip-rule="evenodd" />
            </svg>
            <template v-if="locale"><span class="text-uppercase">{{ locale }}</span></template>
            <template v-else>-</template>
        </template>
        <template v-for="locale in locales" :key="locale">
            <DropdownItem class="text-uppercase small" :active="isActive(locale)" @click="handleChanged(locale)">
                {{ locale }}
            </DropdownItem>
        </template>
    </Dropdown>
</template>

<script>
    import { Dropdown, DropdownItem } from "sharp-ui";

    export default {
        components: {
            Dropdown,
            DropdownItem,
        },
        props: {
            locale: String,
            locales: Array,
        },
        methods: {
            handleChanged(locale) {
                this.$emit('change', locale);
            },
            isActive(locale) {
                return this.locale === locale;
            },
        },
    }
</script>
