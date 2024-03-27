<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from "@/components/ui";
    import EmbedRenderer from '@/content/components/EmbedRenderer.vue';
    import NodeRenderer from "../../NodeRenderer.vue";
    import EmbedFormModal from "./EmbedFormModal.vue";
    import { Form } from "@/form/Form";
    import { Embed, EmbedNodeAttributes } from "@/form/components/fields/editor/extensions/embed/Embed";
    import { computed, nextTick, onMounted, onUnmounted, ref } from "vue";
    import { useParentForm } from "@/form/useParentForm";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { EmbedData } from "@/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";

    const props = defineProps<ExtensionNodeProps<typeof Embed, EmbedNodeAttributes>>();

    const embedManager = useParentEditor().embedManager;
    const embedModal = useParentEditor().embedModal;
    const embedData = computed(() => embedManager.getEmbed(props.node.attrs['data-key'], props.extension.options.embed));

    onMounted(() => {
        embedManager.restoreEmbed(props.node.attrs['data-key'], props.extension.options.embed)
    });

    onUnmounted(() => {
        embedManager.removeEmbed(props.node.attrs['data-key'], props.extension.options.embed);
    });
</script>

<template>
    <NodeRenderer class="editor__node embed-node" :node="node">
        <div class="card">
            <div class="card-body">
                <EmbedRenderer
                    class="embed-node__template"
                    :data="embedData"
                    :embed="extension.options.embed"
                />
                <div class="mt-3">
                    <div class="row row-cols-auto gx-2">
                        <template v-if="extension.options.embed.attributes.length">
                            <div>
                                <Button outline small
                                    @click="embedModal.open({ id: node.attrs['data-key'], embed: extension.options.embed })"
                                >
                                    {{ __('sharp::form.upload.edit_button') }}
                                </Button>
                            </div>
                        </template>
                        <div>
                            <Button variant="danger" outline small @click="deleteNode()">
                                {{ __('sharp::form.upload.remove_button') }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </NodeRenderer>
</template>
