<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { Editor, getMarkRange, getMarkType } from "@tiptap/vue-3";
    import { ref } from "vue";
    import { Input } from "@/components/ui/input";
    import { Label } from "@/components/ui/label";
    import { Toggle } from "@/components/ui/toggle";
    import { LinkIcon } from "lucide-vue-next";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { useId } from "@/composables/useId";
    import { FormFieldProps } from "@/form/types";
    import { FormEditorFieldData } from "@/types";
    import { Range } from "@tiptap/core";

    const props = defineProps<FormFieldProps<FormEditorFieldData> & {
        editor: Editor,
    }>();

    const open = ref(false);
    const label = ref();
    const href = ref();
    const input = ref<InstanceType<typeof Input>>();

    const id = useId('link-dropdown');

    props.editor.on('transaction', () => {
        href.value = props.editor.getAttributes('link')?.href;
    });

    function onOpen() {
        label.value = null;
        const selection = props.editor.state.selection;
        const range = getMarkRange(selection.$from, getMarkType('link', props.editor.schema)) as Range;
        if(range) {
            props.editor.commands.setTextSelection(range);
        }
        props.editor.commands.toggleSelectionHighlight(true);

        setTimeout(() => {
            input.value.$el.focus();
        }, 0);
    }

    function onHide() {
        props.editor.chain()
            .focus()
            .toggleSelectionHighlight(false)
            .run();
    }

    function onSubmit() {
        if(!href.value) {
            open.value = false;
            return;
        }

        if(props.editor.isActive('link')) {
            props.editor.chain()
                .focus()
                .extendMarkRange('link')
                .setLink({ href: href.value })
                .run();

        } else if(props.editor.state.selection.empty) {
            props.editor.chain()
                .focus()
                .insertContent(`<a href="${href.value}">${label.value || href.value}</a>`)
                .run();

        } else {
            props.editor.chain().focus().setLink({ href: href.value }).run();
        }
    }

    function onRemove() {
        open.value = false;
        props.editor.chain().focus().unsetLink().run();
    }

    defineExpose({
        open,
    });
</script>

<template>
    <Popover
        v-model:open="open"
        @update:open="$event ? onOpen() : onHide()"
        :modal="false"
    >
        <PopoverTrigger as-child>
            <Toggle
                :model-value="open || props.editor.isActive('link')"
                size="sm"
                :disabled="props.field.readOnly"
                :title="__('sharp::form.editor.toolbar.link.title')"
            >
                <LinkIcon class="size-4" />
            </Toggle>
        </PopoverTrigger>

        <PopoverContent>
            <form @submit.prevent="onSubmit()">
                <template v-if="!props.editor.isActive('link') && props.editor.state.selection.empty">
                    <div class="mb-4 grid grid-cols-1 gap-3">
                        <Label :for="`${id}-link-label`">
                            {{ __('sharp::form.editor.dialogs.link.text_label') }}
                        </Label>
                        <Input :id="`${id}-link-label`" v-model="label" />
                    </div>
                </template>

                <div class="mb-4 grid grid-cols-1 gap-3">
                    <Label :for="`${id}-href`">
                        {{ __('sharp::form.editor.dialogs.link.url_label') }}
                    </Label>
                    <Input :id="`${id}-href`" v-model="href" placeholder="https://example.org" autocomplete="off" ref="input" />
                </div>

                <div class="mt-4 flex gap-3">
                    <Button type="submit" size="sm">
                        <template v-if="props.editor.isActive('link')">
                            {{ __('sharp::form.editor.dialogs.link.update_button') }}
                        </template>
                        <template v-else>
                            {{ __('sharp::form.editor.dialogs.link.insert_button') }}
                        </template>
                    </Button>
                    <template v-if="props.editor.isActive('link')">
                        <Button type="button" variant="destructive" size="sm" @click="onRemove()">
                            {{ __('sharp::form.editor.dialogs.link.remove_button') }}
                        </Button>
                    </template>
                    <template v-else>
                        <Button type="button" variant="outline" size="sm" @click="open = false; props.editor.commands.focus()">
                            {{ __('sharp::modals.cancel_button') }}
                        </Button>
                    </template>
                </div>
            </form>
        </PopoverContent>
    </Popover>
</template>
