<template>
    <Dropdown
        class="editor__dropdown editor__dropdown--table"
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
                <template v-if="hasLabelInput">
                    <div class="mb-3">
                        <label class="form-label" :for="fieldId('label')">
                            Label
                        </label>
                        <TextInput :id="fieldId('label')" v-model="label" />
                    </div>
                </template>

                <div class="mb-3">
                    <label class="form-label" :for="fieldId('href')">
                        URL Address
                    </label>
                    <TextInput :id="fieldId('href')" v-model="href" placeholder="https://example.org" ref="input" />
                </div>

                <div class="mt-3">
                    <Button type="submit" variant="primary">
                        <template v-if="isEdit">
                            Update
                        </template>
                        <template v-else>
                            Insert
                        </template>
                    </Button>
                    <template v-if="isEdit">
                        <Button type="button" variant="danger" outline @click="handleRemoveClicked">
                            Remove
                        </Button>
                    </template>
                    <template v-else>
                        <Button type="button" variant="light" @click="hide">
                            Cancel
                        </Button>
                    </template>
                </div>
            </b-dropdown-form>
        </template>
    </Dropdown>
</template>

<script>
    import { BFormGroup, BDropdownForm } from 'bootstrap-vue';
    import { Button, Dropdown } from "sharp-ui";
    import TextInput from '../../Text';

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
            fieldId(name) {
                return `${this.id}-${name}`;
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
            handleLinkSubmitted() {
                if(!this.href) {
                    this.$refs.dropdown.hide();
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
