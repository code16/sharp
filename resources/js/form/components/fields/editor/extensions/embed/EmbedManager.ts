import { Form } from "@/form/Form";
import { EmbedData, FormData } from "@/types";
import { api } from "@/api";
import { route } from "@/utils/url";
import debounce from "lodash/debounce";

type ContentEmbed = {
    id: string,
    embed: EmbedData,
    state: 'registered' | 'resolving' | 'resolved',
    value: EmbedData['value'],
}

export class EmbedManager {
    contentEmbeds: { [embedKey:string]: ContentEmbed[] } = {}
    queues: { [embedKey: string]: PromiseWithResolvers<true> & { debounced?:() => void } } = {}

    parentForm: Form;

    onEmbedsUpdated: (embeds: { [embedKey: string]: Array<EmbedData['value']> }) => any

    uniqueId = 0;

    constructor(parentForm: Form, { onEmbedsUpdated }: { onEmbedsUpdated: EmbedManager['onEmbedsUpdated'] }) {
        this.parentForm = parentForm;
        this.onEmbedsUpdated = onEmbedsUpdated;
    }

    get serializedEmbeds() {
        return Object.fromEntries(
            Object.entries(this.contentEmbeds).map(([embedKey, contentEmbeds]) =>
                [embedKey, contentEmbeds.map(contentEmbed => contentEmbed.value)]
            )
        )
    }

    getEmbedUniqueId(embed: EmbedData) {
        return `${embed.key}-${this.uniqueId++}`;
    }

    getContentEmbed(uniqueId: string) {
        return Object.values(this.contentEmbeds).flat().find(embed => embed.id === uniqueId);
    }

    async registerContentEmbed(uniqueId: string, embed: EmbedData, contentEmbedAttributes: EmbedData['value']): Promise<EmbedData['value'] | null> {
        if(this.getContentEmbed(uniqueId)) {
            return null;
        }

        if(!this.contentEmbeds[embed.key]) {
            this.contentEmbeds[embed.key] = [];
        }

        this.contentEmbeds[embed.key].push({
            id: uniqueId,
            embed,
            state: 'registered',
            value: contentEmbedAttributes,
        });

        await this.queueResolve(embed.key);

        return this.getContentEmbed(uniqueId).value;
    }

    updateContentEmbeds(embedKey: string, updatedContentEmbeds: ContentEmbed[]) {
        this.contentEmbeds[embedKey] = this.contentEmbeds[embedKey].map(contentEmbed =>
            updatedContentEmbeds.find(updatedContentEmbed => updatedContentEmbed.id === contentEmbed.id) ?? contentEmbed
        );
    }

    async queueResolve(embedKey: string) {
        this.queues[embedKey] ??= {
            ...Promise.withResolvers(),
            debounced: debounce(() => {
                const resolve = this.queues[embedKey].resolve;
                this.queues[embedKey] = null;
                this.resolveRegisteredContentEmbeds(embedKey)
                    .finally(() => {
                        resolve(true);
                    });
            }),
        };

        this.queues[embedKey].debounced();

        await this.queues[embedKey].promise;
    }

    async resolveRegisteredContentEmbeds(embedKey: string) {
        const { entityKey, instanceId } = this.parentForm;
        const contentEmbeds = this.contentEmbeds[embedKey].filter(contentEmbed => contentEmbed.state === 'registered');

        if(!contentEmbeds?.length) {
            return;
        }

        this.updateContentEmbeds(embedKey, contentEmbeds.map((contentEmbed) => ({
            ...contentEmbed,
            state: 'resolving',
        })));

        const embeds = await api.post(
            instanceId
                ? route('code16.sharp.api.embed.instance.show', { embedKey, entityKey, instanceId })
                : route('code16.sharp.api.embed.show', { embedKey, entityKey }),
            { embeds: contentEmbeds.map(contentEmbed => contentEmbed.value), form: true }
        )
            .then(response => response.data.embeds);

        this.updateContentEmbeds(embedKey, contentEmbeds.map((contentEmbed, i) => ({
            ...contentEmbed,
            state: 'resolved',
            value: embeds[i],
        })));
    }

    removeEmbed(uniqueId: string, embed: EmbedData) {
        this.contentEmbeds[embed.key] = this.contentEmbeds[embed.key]?.filter(contentEmbed => contentEmbed.id !== uniqueId);
        this.onEmbedsUpdated(this.serializedEmbeds);
    }

    postResolveForm(embed: EmbedData, contentEmbedAttributes: EmbedData['value']): Promise<FormData> {
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

    async postForm(uniqueId: string, embed: EmbedData, data: EmbedData['value']): Promise<EmbedData['value']> {
        const { entityKey, instanceId } = this.parentForm;
        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                { ...data }
            )
            .then(response => response.data);

        this.contentEmbeds[embed.key] = this.contentEmbeds[embed.key].map(contentEmbed =>
            contentEmbed.id === uniqueId
                ? { ...contentEmbed, value: responseData }
                : contentEmbed
        );

        this.onEmbedsUpdated(this.serializedEmbeds);

        return responseData;
    }
}
