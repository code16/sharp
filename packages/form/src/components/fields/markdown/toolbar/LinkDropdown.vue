<template>
    <Dropdown
        class="editor__link-dropdown"
        variant="light"
        :active="active"
        :show-caret="false"
        @show="handleLinkDropdownShow"
        @shown="handleLinkDropdownShown"
    >
        <template v-slot:text>
            <slot />
        </template>

        <b-dropdown-form @submit.prevent="handleLinkSubmitted">
            <b-form-group :id="`${id}-label`" class="mb-3" label="Label" v-slot="{ id }">
                <TextInput :id="id" v-model="label" />
            </b-form-group>

            <b-form-group :id="`${id}-href`" class="mb-3" label="Address" v-slot="{ id }">
                <TextInput :id="id" v-model="href" placeholder="https://example.org" ref="input" />
            </b-form-group>

            <div class="mt-3">
                <template v-if="active">
                    <Button type="button" variant="primary" @click="handleRemoveClicked">
                        Remove
                    </Button>
                </template>
                <Button type="submit" variant="primary">
                    Insert
                </Button>
            </div>
        </b-dropdown-form>
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
            }
        },
        methods: {
            fieldId() {

            },
            handleLinkDropdownShow() {
                if(this.active) {
                    console.log(this.editor.getAttributes('link'));
                }
                this.href = null;
            },
            handleLinkDropdownShown() {
                setTimeout(() => {
                    this.$refs.input.focus();
                }, 0);
            },
            handleLinkSubmitted() {
                this.$emit('submit', {
                    href: this.href,
                    label: this.label,
                });
            },
            handleRemoveClicked() {

            },
        }
    }
</script>
