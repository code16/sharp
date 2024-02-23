import { Form } from "@/form/Form";
import { EmbedData, FormData } from "@/types";
import { api } from "@/api";
import { route } from "@/utils/url";
import { EmbedAttributesData } from "@/form/components/fields/editor/extensions/embed/Embed";


export class EmbedManager {
    embeds: { [embedKey: string]: EmbedAttributesData[] } = {}

    allInitialEmbedsResolved = Promise.withResolvers();

    parentForm: Form;

    onEmbedsUpdated: (embeds: { [embedKey: string]: EmbedAttributesData[] }) => any

    editorCreated = false;

    uniqueId = 0;

    constructor(parentForm: Form, { onEmbedsUpdated }: { onEmbedsUpdated: EmbedManager['onEmbedsUpdated'] }) {
        this.parentForm = parentForm;
        this.onEmbedsUpdated = onEmbedsUpdated;
    }

    getEmbedUniqueId(embed: EmbedData) {
        return `${embed.key}-${this.uniqueId++}`;
    }

    async registerContentEmbed(uniqueId: string, embed: EmbedData, contentEmbedAttributes: EmbedAttributesData): Promise<EmbedAttributesData | null> {
        if(this.editorCreated) {
            return null;
        }

        const index = this.embeds?.[embed.key]?.length ?? 0;

        this.embeds[embed.key] = [
            ...(this.embeds[embed.key] ?? []),
            { ...contentEmbedAttributes, _uniqueId: uniqueId },
        ]

        await this.allInitialEmbedsResolved.promise;

        return this.embeds[embed.key][index];
    }

    async resolveAllInitialContentEmbeds() {
        const { entityKey, instanceId } = this.parentForm;

        await Promise.allSettled(
            Object.entries(this.embeds)
                .map(([embedKey, embeds]) => async () => {
                    this.embeds[embedKey] = await api.post(
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

    removeEmbed(uniqueId: string, embed: EmbedData) {
        this.embeds[embed.key] = this.embeds[embed.key]?.filter(embedData => embedData._uniqueId !== uniqueId);
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

    async postForm(uniqueId: string, embed: EmbedData, data: EmbedAttributesData): Promise<EmbedAttributesData> {
        const { entityKey, instanceId } = this.parentForm;
        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                { ...data }
            )
            .then(response => response.data);

        const index = this.embeds[embed.key]?.findIndex(embed => embed._uniqueId === uniqueId) ?? -1;

        if(index < 0) {
            this.embeds[embed.key] = [...(this.embeds[embed.key] ?? []), { ...responseData, _uniqueId: uniqueId }];
        } else {
            this.embeds[embed.key][index] = responseData;
        }

        this.onEmbedsUpdated(this.embeds);

        return responseData;
    }
}
