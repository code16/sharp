<template>
    <div>
        <template v-for="locale in locales">
            <div v-show="isActive(locale)" :key="locale">
                <slot :editor="localizedEditors[locale]" />
            </div>
        </template>
    </div>
</template>

<script>
    import SharpEditor from "./Editor";

    export default {
        components: {
            SharpEditor,
        },
        props: {
            value: Object,
            locales: Array,
            locale: String,
            createEditor: Function,
        },
        data() {
            return {
                localizedEditors: {},
            }
        },
        methods: {
            isActive(locale) {
                return this.locale === locale;
            },
        },
        created() {
            this.localizedEditors = Object.fromEntries(
                this.locales.map(locale => [
                    locale,
                    this.createEditor({
                        content: this.value?.text?.[locale] ?? null,
                    }),
                ])
            );
        },
        beforeDestroy() {
            Object.values(this.localizedEditors).forEach(editor => {
                editor.destroy()
            });
        }
    }
</script>
