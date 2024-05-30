<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { Editor } from "@tiptap/vue-3";
    import { ref } from "vue";
    import { Selection } from "@tiptap/pm/state";
    import { DropdownMenu } from "@/components/ui/dropdown-menu";
    import { Input } from "@/components/ui/input";
    import { Label } from "@/components/ui/label";

    const props = defineProps<{
        id: string,
        editor: Editor,
        active: boolean,
        disabled: boolean,
    }>();

    const open = ref(false);
    const label = ref();
    const href = ref();
    const hasSelectedText = ref(false);
    const inserted = ref(false);
    const selection = ref<Selection>(null);
    const input = ref<InstanceType<typeof Input>>();

    function hide(focusEditor = true) {
        open.value = false;
        if(focusEditor) {
            props.editor.chain().focus().run();
        }
    }

    function onShow() {
        selection.value = props.editor.state.selection
        href.value = null;
        inserted.value = false;
        hasSelectedText.value = !selection.value.empty;

        if(this.active) {
            const attrs = this.editor.getAttributes('link');
            href.value = attrs?.href;
            inserted.value = true;
        }

        if(hasSelectedText.value) {
            // props.editor.commands.setLink({ href:'#' });
        }

        setTimeout(() => {
            input.value.$el.focus();
        }, 0);
    }

    function onHide() {
        if(!inserted.value && hasSelectedText.value) {
            const { from, to } = props.editor.state.selection;
            this.editor.chain()
                .setTextSelection(selection.value.from, selection.value.to)
                .unsetLink()
                .setTextSelection(from, to)
                .run();
        }
    }

    function onSubmit() {
        if(!href.value) {
            hide();
            return;
        }
        this.$emit('submit', {
            href: this.href,
            label: this.label,
        });
        this.inserted = true;
    }
</script>

<template>
    <DropdownMenu
        v-model:open="open"
        @update:open="$event ? onShow() : onHide()"
    >
        <template v-slot:text>
            <slot />
        </template>

        <template>
            <form @submit.prevent="handleLinkSubmitted">
                <template v-if="!active && !hasSelectedText">
                    <div class="grid grid-cols-1 gap-3">
                        <Label :for="`${id}-link-label`">
                            {{ __('sharp::form.editor.dialogs.link.text_label') }}
                        </Label>
                        <Input :id="`${id}-link-label`" v-model="label" />
                    </div>
                </template>

                <div class="mb-3">
                    <Label :for="`${id}-href`">
                        {{ __('sharp::form.editor.dialogs.link.url_label') }}
                    </Label>
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
                                <Button type="button" variant="outline" size="sm" @click="hide()">
                                    {{ __('sharp::modals.cancel_button') }}
                                </Button>
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </template>
    </DropdownMenu>
</template>

<script lang="ts">
    export default {
        methods: {
            handleLinkSubmitted() {

            },
            handleRemoveClicked() {
                this.editor.chain().focus().unsetLink().run()
                this.$emit('remove');
            },
        }
    }
</script>
