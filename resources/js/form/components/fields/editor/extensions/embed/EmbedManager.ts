import { Form } from "@/form/Form";
import { EmbedData, FormData, FormUploadFieldValueData } from "@/types";
import { api } from "@/api";
import { route } from "@/utils/url";
import { EmbedAttributesData } from "@/form/components/fields/editor/extensions/embed/Embed";

export class EmbedManager {
    initialContentEmbeds: {
        [embedKey: string]: Array<EmbedAttributesData>
    } = {};

    allInitialEmbedsResolved = Promise.withResolvers();

    parentForm: Form;

    onEmbedUpdated: (uploadedOrTransformedFiles: FormUploadFieldValueData[]) => any;

    editorCreated = false;

    constructor(parentForm: Form, { onEmbedUpdated }: { onEmbedUpdated: EmbedManager['onEmbedUpdated'] }) {
        this.parentForm = parentForm;
        this.onEmbedUpdated = onEmbedUpdated;
    }

    async registerContentEmbed(embed: EmbedData, contentEmbedAttributes: EmbedAttributesData): Promise<EmbedAttributesData | null> {
        if(this.editorCreated) {
            return null;
        }

        this.initialContentEmbeds[embed.key] = [
            ...(this.initialContentEmbeds[embed.key] ?? []),
            contentEmbedAttributes,
        ];
        const index = this.initialContentEmbeds[embed.key].length - 1;

        await this.allInitialEmbedsResolved.promise;

        return this.initialContentEmbeds[embed.key][index];
    }

    async resolveAllInitialContentEmbeds() {
        const { entityKey, instanceId } = this.parentForm;

        await Promise.allSettled(
            Object.entries(this.initialContentEmbeds)
                .map(([embedKey, embeds]) => async () => {
                    this.initialContentEmbeds[embedKey] = await api.post(
                        instanceId
                            ? route('code16.sharp.api.embed.instance.show', { embedKey, entityKey, instanceId })
                            : route('code16.sharp.api.embed.show', { embedKey, entityKey }),
                        { embeds, form: true }
                    )
                        .then(response => response.data.embeds)
                })
        );

        this.allInitialEmbedsResolved.resolve(true);
    }

    postResolveForm(embed: EmbedData, contentEmbedAttributes: EmbedAttributesData): Promise<FormData> {
        const { entityKey, instanceId } = this.parentForm;

        return api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.show', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.show', { embedKey: embed.key, entityKey }),
                { ...contentEmbedAttributes }
            )
            .then(response => response.data);
    }

    async postForm(embed: EmbedData, data: FormData['data'], embedForm: Form): Promise<EmbedAttributesData> {
        const { entityKey, instanceId } = this.parentForm;
        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                { ...data }
            )
            .then(response => response.data);

        this.onEmbedUpdated(embedForm.getAllUploadedOrTransformedFiles(responseData));

        return responseData;
    }
}
