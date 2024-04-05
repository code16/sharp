<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { Dropdown } from "@/components/ui";
    import TextInput from '../../text/TextInput.vue';
    import {Editor} from "@tiptap/vue-3";
    import {ref} from "vue";

    const props = defineProps<{
        id: string,
        editor: Editor,
        active: boolean,
        disabled: boolean,
    }>();

    const label = ref();
    const href = ref();
    const hasSelectedText = ref(false);
    const inserted = ref(false);
    const selection = ref(null);
    const dropdown = ref<InstanceType<typeof Dropdown>>();
    const input = ref<InstanceType<typeof TextInput>>();

    function hide(focusEditor = true) {
        dropdown.value.close();
        if(focusEditor) {
            this.editor.chain().focus().run();
        }
    }

    function onShow() {
        href.value = null;
        inserted.value = false;
        hasSelectedText.value = !selection.empty;

        if(this.active) {
            const attrs = this.editor.getAttributes('link');
            href.value = attrs?.href;
            inserted.value = true;
        }

        if(hasSelectedText.value) {
            props.editor.commands.setLink({ href:'#' });
            selection.value = {
                from: props.editor.state.selection.from,
                to: props.editor.state.selection.to,
            }
        }
    }

    function onShown() {
        setTimeout(() => {
            input.value.focus();
        }, 0);
    }
</script>

<template>
    <Dropdown
        class="editor__dropdown editor__dropdown--link"
        variant="light"
        :active="active"
        :disabled="disabled"
        v-bind="$attrs"
        @show="onShow"
        @shown="onShown"
        @hide="handleDropdownHide"
        ref="dropdown"
    >
        <template v-slot:text>
            <slot />
        </template>

        <template>
            <form @submit.prevent="handleLinkSubmitted">
<!--                <template v-if="inserted">-->
<!--                    <button class="btn-close position-absolute end-0 top-0 p-2 fs-8"-->
<!--                        type="button"-->
<!--                        @click="handleCancelClicked"-->
<!--                    >-->
<!--                        <span class="visually-hidden">{{ __('sharp::modals.cancel_button') }}</span>-->
<!--                    </button>-->
<!--                </template>-->

                <template v-if="!active && !hasSelectedText">
                    <div class="mb-3">
                        <label class="form-label" :for="`${id}-link-label`">
                            {{ __('sharp::form.editor.dialogs.link.text_label') }}
                        </label>
                        <TextInput :id="`${id}-link-label`" v-model="label" />
                    </div>
                </template>

                <div class="mb-3">
                    <label class="form-label" :for="`${id}-href`">
                        {{ __('sharp::form.editor.dialogs.link.url_label') }}
                    </label>
                    <TextInput :id="`${id}-href`" v-model="href" placeholder="https://example.org" autocomplete="off" ref="input" />
                </div>

                <div class="mt-3">
                    <div class="row g-2 flex-sm-nowrap">
                        <div class="col-auto">
                            <Button type="submit" size="sm">
                                <template v-if="inserted">
                                    {{ __('sharp::form.editor.dialogs.link.update_button') }}
                                </template>
                                <template v-else>
                                    {{ __('sharp::form.editor.dialogs.link.insert_button') }}
                                </template>
                            </Button>
                        </div>
                        <div class="col-auto">
                            <template v-if="inserted">
                                <Button type="button" variant="destructive" size="sm" @click="handleRemoveClicked">
                                    {{ __('sharp::form.editor.dialogs.link.remove_button') }}
                                </Button>
                            </template>
                            <template v-else>
                                <Button type="button" variant="outline" size="sm" @click="handleCancelClicked">
                                    {{ __('sharp::modals.cancel_button') }}
                                </Button>
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </template>
    </Dropdown>
</template>

<script lang="ts">
    export default {
        methods: {
            handleDropdownShown() {

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
                this.editor.chain().focus().unsetLink().run()
                this.$emit('remove');
            },
        }
    }
</script>
