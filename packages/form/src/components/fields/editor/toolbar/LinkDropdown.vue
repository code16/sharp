<template>
    <Dropdown
        class="editor__dropdown editor__dropdown--link"
        variant="light"
        :active="active"
        v-bind="$attrs"
        @show="handleDropdownShow"
        @shown="handleDropdownShown"
        @hide="handleDropdownHide"
        ref="dropdown"
    >
        <template v-slot:text>
            <slot />
        </template>

        <template v-slot:default="{ hide }">
            <b-dropdown-form @submit.prevent="handleLinkSubmitted">
                <template v-if="isEdit">
                    <button class="btn-close position-absolute end-0 top-0 p-2 fs-8"
                        type="button"
                        @click="handleCancelClicked"
                    >
                        <span class="visually-hidden">{{ lang('modals.cancel_button', 'Cancel') }}</span>
                    </button>
                </template>

                <template v-if="hasLabelInput">
                    <div class="mb-3">
                        <label class="form-label" :for="fieldId('label')">
                            {{ lang('form.editor.dialogs.link.text_label', 'Text') }}
                        </label>
                        <TextInput :id="fieldId('label')" v-model="label" />
                    </div>
                </template>

                <div class="mb-3">
                    <label class="form-label" :for="fieldId('href')">
                        {{ lang('form.editor.dialogs.link.url_label', 'URL') }}
                    </label>
                    <TextInput :id="fieldId('href')" v-model="href" placeholder="https://example.org" autocomplete="off" ref="input" />
                </div>

                <div class="mt-3">
                    <div class="row g-2 flex-sm-nowrap">
                        <div class="col-auto">
                            <Button type="submit" small variant="primary">
                                <template v-if="isEdit">
                                    {{ lang('form.editor.dialogs.link.update_button', 'Update') }}
                                </template>
                                <template v-else>
                                    {{ lang('form.editor.dialogs.link.insert_button', 'Insert link') }}
                                </template>
                            </Button>
                        </div>
                        <div class="col-auto">
                            <template v-if="isEdit">
                                <Button type="button" small variant="danger" outline @click="handleRemoveClicked">
                                    {{ lang('form.editor.dialogs.link.remove_button', 'Remove link') }}
                                </Button>
                            </template>
                            <template v-else>
                                <Button type="button" small variant="light" @click="handleCancelClicked">
                                    {{ lang('modals.cancel_button', 'Cancel') }}
                                </Button>
                            </template>
                        </div>
                    </div>
                </div>
            </b-dropdown-form>
        </template>
    </Dropdown>
</template>

<script>
    import { BFormGroup, BDropdownForm } from 'bootstrap-vue';
    import { Button, Dropdown } from "sharp-ui";
    import TextInput from '../../Text';
    import { lang } from "sharp";

    export default {
        components: {
            Button,
            Dropdown,
            BDropdownForm,
            BFormGroup,
            TextInput,
        },
        props: {
            id: String,
            active: Boolean,
            editor: Object,
        },
        data() {
            return {
                label: null,
                href: null,
                hasSelectedText: false,
                inserted: false,
                selection: null,
            }
        },
        computed: {
            hasLabelInput() {
                return !this.active && !this.hasSelectedText;
            },
            isEdit() {
                return this.inserted;
            },
        },
        methods: {
            lang,
            fieldId(name) {
                return `${this.id}-${name}`;
            },
            hide(focusEditor = true) {
                this.$refs.dropdown.hide();
                if(focusEditor) {
                    this.editor.chain().focus().run();
                }
            },
            handleDropdownShow() {
                const selection = this.editor.state.selection;

                this.href = null;
                this.inserted = false;
                this.hasSelectedText = !selection.empty;

                if(this.active) {
                    const attrs = this.editor.getAttributes('link');
                    this.href = attrs?.href;
                    this.inserted = true;
                }

                if(this.hasSelectedText) {
                    this.editor.commands.setLink({ href:'#' });
                    this.selection = {
                        from: selection.from,
                        to: selection.to,
                    }
                }
            },
            handleDropdownShown() {
                setTimeout(() => {
                    this.$refs.input.focus();
                }, 0);
            },
            handleDropdownHide() {
                if(!this.inserted && this.hasSelectedText) {
                    const { from, to } = this.editor.state.selection;
                    this.editor.chain()
                        .setTextSelection(this.selection.from, this.selection.to)
                        .unsetLink()
                        .setTextSelection(from, to)
                        .run();
                }
            },
            handleCancelClicked() {
                this.hide();
            },
            handleLinkSubmitted() {
                if(!this.href) {
                    this.hide();
                    return;
                }
                this.$emit('submit', {
                    href: this.href,
                    label: this.label,
                });
                this.inserted = true;
            },
            handleRemoveClicked() {
                this.$emit('remove');
            },
        }
    }
</script>
