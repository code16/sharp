<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { Editor } from "@tiptap/vue-3";
    import { ref } from "vue";
    import { Selection } from "@tiptap/pm/state";
    import { Input } from "@/components/ui/input";
    import { Label } from "@/components/ui/label";
    import { Toggle } from "@/components/ui/toggle";
    import { LinkIcon } from "lucide-vue-next";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { useId } from "@/composables/useId";
    import { FormFieldProps } from "@/form/types";
    import { FormEditorFieldData } from "@/types";

    const props = defineProps<FormFieldProps<FormEditorFieldData> & {
        editor: Editor,
    }>();

    const open = ref(false);
    const label = ref();
    const href = ref();
    const hasSelectedText = ref(false);
    const inserted = ref(false);
    const selection = ref<Selection>(null);
    const input = ref<InstanceType<typeof Input>>();

    const id = useId('link-dropdown');

    function onShow() {
        selection.value = props.editor.state.selection
        href.value = null;
        inserted.value = false;
        hasSelectedText.value = !selection.value.empty;

        if(props.editor.isActive('link')) {
            const attrs = props.editor.getAttributes('link');
            href.value = attrs?.href;
            inserted.value = true;
        }

        if(hasSelectedText.value) {
            props.editor.commands.togglePendingLink(true);
        }

        setTimeout(() => {
            input.value.$el.focus();
        }, 0);
    }

    function onHide() {
        props.editor.commands.togglePendingLink(false);
    }

    function onSubmit() {
        if(!href.value) {
            open.value = false;
            props.editor.commands.focus();
            return;
        }

        const selection = props.editor.state.tr.selection;

        if(props.editor.isActive('link')) {
            props.editor.chain()
                .focus()
                .extendMarkRange('link')
                .setLink({ href: href.value })
                .run();

        } else if(selection.empty) {
            props.editor.chain()
                .focus()
                .insertContent(`<a href="${href.value}">${label.value || href.value}</a>`)
                .run();

        } else {
            props.editor.chain().focus().setLink({ href: href.value }).run();
        }

        inserted.value = true;
    }

    function onRemove() {
        open.value = false;
        props.editor.chain().focus().unsetLink().run();
    }
</script>

<template>
    <Popover
        v-model:open="open"
        @update:open="$event ? onShow() : onHide()"
        :modal="false"
    >
        <PopoverTrigger as-child>
            <Toggle
                :model-value="props.editor.isActive('link')"
                :disabled="props.field.readOnly"
                :title="__('sharp::form.editor.toolbar.link.title')"
            >
                <LinkIcon class="size-4" />
            </Toggle>
        </PopoverTrigger>

        <PopoverContent>
            <form @submit.prevent="onSubmit()">
                <template v-if="!inserted && !hasSelectedText">
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
                        <template v-if="inserted">
                            {{ __('sharp::form.editor.dialogs.link.update_button') }}
                        </template>
                        <template v-else>
                            {{ __('sharp::form.editor.dialogs.link.insert_button') }}
                        </template>
                    </Button>
                    <template v-if="inserted">
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
