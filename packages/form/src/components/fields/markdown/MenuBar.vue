<template>
    <div class="editor__toolbar">
        <template v-for="group in toolbarGroups">
            <div class="btn-group">
                <template v-for="button in group">
                    <button class="btn btn-light"
                        :class="{ 'active': isActive(button) }"
                        @click="handleClicked(button)"
                    >
                        <i :class="getIcon(button)"></i>
                    </button>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
    import { buttons } from './config';

    export default {
        props: {
            editor: Object,
            toolbar: Array,
        },
        computed: {
            toolbarGroups() {
                return this.toolbar.reduce((res, btn) => {
                    if(btn === '|') {
                        return [...res, []];
                    }
                    res[res.length - 1].push(btn);
                    return res;
                }, [[]]);
            },
        },
        methods: {
            getIcon(button) {
                return buttons[button]?.icon;
            },
            isActive(button) {
                return buttons[button]?.isActive(this.editor);
            },
            handleClicked(button) {
                buttons[button]?.command(this.editor);
            },
        },
    }
</script>
