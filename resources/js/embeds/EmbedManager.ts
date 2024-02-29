import { Form } from "@/form/Form";
import { EmbedData, FormData } from "@/types";
import { api } from "@/api";
import { route } from "@/utils/url";
import { parseAttributeValue } from "@/embeds/utils/attributes";
import { hyphenate } from "@/utils";
import { Show } from "@/show/Show";

type ContentEmbed = {
    id: string,
    resolved?: PromiseWithResolvers<true>,
    embed: EmbedData,
    removed?: boolean,
    value: EmbedData['value'],
}

export class EmbedManager<Root extends Form | Show> {
    contentEmbeds: { [id: string] : ContentEmbed } = {}
    embeds: { [embedKey:string]: EmbedData } = {};
    onEmbedsUpdated: Root extends Form ? (embeds: { [embedKey: string]: Array<EmbedData['value']> }) => any : null
    root: Form | Show;
    uniqueId = 0

    constructor(
        root: Root,
        embeds: EmbedManager<Root>['embeds'],
        config: {
            onEmbedsUpdated: EmbedManager<Root>['onEmbedsUpdated']
        } = { onEmbedsUpdated: null }) {
        this.root = root;
        this.embeds = embeds;
        this.onEmbedsUpdated = config.onEmbedsUpdated;
    }

    get serializedEmbeds() {
        return Object.values(this.contentEmbeds)
            .reduce((res, contentEmbed) => ({
                ...res,
                [contentEmbed.embed.key]: !contentEmbed.removed
                    ? [...(res[contentEmbed.embed.key] ?? []), contentEmbed.value]
                    : res[contentEmbed.embed.key],
            }), {});
    }

    newId(embed: EmbedData) {
        return `${embed.key}-${this.uniqueId++}`;
    }

    withEmbedUniqueId(content: string, toggle: boolean = true): string {
        const parser = new DOMParser();
        const document = parser.parseFromString(content, 'text/html');

        Object.values(this.embeds).forEach(embed => {
            document.querySelectorAll(embed.tag).forEach(element => {
                if(toggle) {
                    element.setAttribute('data-unique-id', this.newId(embed));
                } else {
                    element.removeAttribute('data-unique-id');
                }
            });
        });

        return document.body.innerHTML;
    }

    serializeContent(content: string): string {
        return this.withEmbedUniqueId(content, false);
    }

    async resolveEmbeds(content: string) {
        const { entityKey, instanceId } = this.root;
        const parser = new DOMParser();
        const document = parser.parseFromString(content, 'text/html');

        Object.values(this.embeds)
            .map(embed => {
                const contentEmbeds = [...document.querySelectorAll(embed.tag)]
                    .map(element => ({
                        id: element.getAttribute('data-unique-id'),
                        resolved: Promise.withResolvers<true>(),
                        embed,
                        value: Object.fromEntries(
                            embed.attributes.map(attributeName =>
                                [attributeName, parseAttributeValue(element.getAttribute(hyphenate(attributeName)))]
                            )
                        ) as EmbedData['value']
                    }));

                if(!contentEmbeds.length) {
                    return;
                }

                this.contentEmbeds = {
                    ...this.contentEmbeds,
                    ...Object.fromEntries(contentEmbeds.map(contentEmbed => [contentEmbed.id, contentEmbed])),
                }

                return (async () => {
                    const resolvedEmbeds = await api.post(
                        instanceId
                            ? route('code16.sharp.api.embed.instance.show', { embedKey: embed.key, entityKey, instanceId })
                            : route('code16.sharp.api.embed.show', { embedKey: embed.key, entityKey }),
                        {
                            embeds: contentEmbeds.map(contentEmbed => contentEmbed.value),
                            form: this.root instanceof Form,
                        }
                    )
                        .then(response => response.data.embeds) as EmbedData['value'][];

                    resolvedEmbeds.forEach((resolvedEmbed, index) => {
                        this.contentEmbeds[contentEmbeds[index].id].value = resolvedEmbed;
                        this.contentEmbeds[contentEmbeds[index].id].resolved.resolve(true);
                    });
                })();
            });
    }


    async getResolvedEmbed(id: string): Promise<EmbedData['value'] | undefined> {
        if(this.contentEmbeds[id]?.removed) {
            this.contentEmbeds[id].removed = false;
        }

        await this.contentEmbeds[id]?.resolved?.promise;

        return this.contentEmbeds[id]?.value;
    }

    removeEmbed(id: string) {
        this.contentEmbeds[id] = {
            ...this.contentEmbeds[id],
            removed: true,
        }
        this.onEmbedsUpdated(this.serializedEmbeds);
    }

    postResolveForm(id: string, embed: EmbedData): Promise<FormData> {
        const { entityKey, instanceId } = this.root;

        return api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.show', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.show', { embedKey: embed.key, entityKey }),
                { ...this.contentEmbeds[id].value }
            )
            .then(response => response.data);
    }

    async postForm(id: string, embed: EmbedData, data: EmbedData['value']): Promise<EmbedData['value']> {
        const { entityKey, instanceId } = this.root;
        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                { ...data }
            )
            .then(response => response.data);

        this.contentEmbeds[id] = {
            id,
            embed,
            value: responseData,
        }

        this.onEmbedsUpdated(this.serializedEmbeds);

        return responseData;
    }
}
