<template>
    <div>
        <template v-if="editor">
            <slot :editor="editor" />
            <input type="hidden" :name="name" :value="value && value.text">
        </template>
        <template v-else>
            <template v-for="locale in locales">
                <div v-show="isActive(locale)" :key="locale">
                    <slot :editor="localizedEditors[locale]" />
                </div>
            </template>
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
            editor: Object,
            name: String,
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
            if(!this.editor && this.locales) {
                this.localizedEditors = Object.fromEntries(
                    this.locales.map(locale => [
                        locale,
                        this.createEditor({
                            content: this.value?.text?.[locale] ?? null,
                        }),
                    ])
                );
            }
        },
        beforeDestroy() {
            Object.values(this.localizedEditors).forEach(editor => {
                editor.destroy()
            });
        }
    }
</script>
