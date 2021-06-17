<template>
    <div class="editor__toolbar">
        <template v-for="group in toolbarGroups">
            <div class="btn-group">
                <template v-for="button in group">
                    <template v-if="button === 'link'">
                        <Dropdown
                            variant="light"
                            :active="isActive(button)"
                            :show-caret="false"
                            @show="handleLinkDropdownShow"
                            :key="button"
                        >
                            <template v-slot:text>
                                <i :class="getIcon(button)"></i>
                            </template>

                            <b-dropdown-form @submit.prevent="handleLinkSubmitted">
                                <input v-model="href" type="text" class="form-control">
                                <Button variant="primary">
                                    Insert
                                </Button>
                            </b-dropdown-form>
                        </Dropdown>
                    </template>
                    <template v-else>
                        <Button
                            variant="light"
                            :active="isActive(button)"
                            @click="handleClicked(button)"
                            :key="button"
                        >
                            <i :class="getIcon(button)"></i>
                        </Button>
                    </template>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
    import { BDropdownForm } from 'bootstrap-vue';
    import { Button, Dropdown } from "sharp-ui";
    import { buttons } from './config';

    export default {
        components: {
            Button,
            Dropdown,
            BDropdownForm,
        },
        props: {
            editor: Object,
            toolbar: Array,
            bubbleMenu: Boolean,
        },
        data() {
            return {
                href: null,
            }
        },
        computed: {
            toolbarGroups() {
                return this.toolbar
                    .filter(button => {
                        if(this.bubbleMenu) {
                            return buttons[button]?.bubbleMenu;
                        }
                        return true;
                    })
                    .reduce((res, btn) => {
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
            handleLinkDropdownShow() {
                this.href = null;
            },
            handleLinkSubmitted() {
                buttons.link.command(this.editor, this.href);
            },
        },
    }
</script>
