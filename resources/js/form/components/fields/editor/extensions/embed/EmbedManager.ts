import { Form } from "@/form/Form";
import { EmbedData, FormData } from "@/types";
import { api } from "@/api";
import { route } from "@/utils/url";

export class EmbedManager {
    contentEmbeds: {
        [embedKey: string]: Array<{ [attrName: string]: any }>
    };

    allEmbedResolved = Promise.withResolvers();

    parentForm: Form;

    onEmbedUpdated;

    constructor(parentForm: Form, { onEmbedUpdated }) {
        this.parentForm = parentForm;
        this.onEmbedUpdated = onEmbedUpdated;
    }

    async registerContentEmbed(embed: EmbedData, contentEmbedAttributes) {
        const index = (this.contentEmbeds[embed.key] ?? []).length;
        this.contentEmbeds[embed.key] = [
            ...(this.contentEmbeds[embed.key] ?? []),
            contentEmbedAttributes,
        ]

        await this.allEmbedResolved.promise;

        return this.contentEmbeds[embed.key][index];
    }

    postResolveForm(embed: EmbedData, contentEmbedAttributes) {
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

    postForm(embed: EmbedData, data: FormData['data'], embedForm: Form) {
        const { entityKey, instanceId } = this.parentForm;

        return api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                { ...data }
            )
            .then(response => {
                this.onEmbedUpdated(embedForm.getAllUploadedOrTransformedFiles(response.data));

                return response.data;
            });
    }

    async resolveAll() {
        const { entityKey, instanceId } = this.parentForm;

        await Promise.allSettled(
            Object.entries(this.contentEmbeds)
                .map(([embedKey, embeds]) => async () => {
                    this.contentEmbeds[embedKey] = await api.post(
                        instanceId
                            ? route('code16.sharp.api.embed.instance.show', { embedKey, entityKey, instanceId })
                            : route('code16.sharp.api.embed.show', { embedKey, entityKey }),
                        { embeds, form: true }
                    )
                        .then(response => response.data.embeds)
                })
        );

        this.allEmbedResolved.resolve(true);
    }
}
