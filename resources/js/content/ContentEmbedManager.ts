import { Form } from "@/form/Form";
import { EmbedData, FormData } from "@/types";
import { api } from "@/api";
import { route } from "@/utils/url";
import { parseAttributeValue, serializeUploadAttributeValue } from "@/content/utils/attributes";
import { hyphenate } from "@/utils";
import { Show } from "@/show/Show";
import { MaybeLocalizedContent } from "@/content/types";
import { ContentManager } from "@/content/ContentManager";

type ContentEmbed = {
    id: string,
    resolved?: PromiseWithResolvers<true>,
    embed: EmbedData,
    removed?: boolean,
    value: EmbedData['value'],
}

export class ContentEmbedManager<Root extends Form | Show> extends ContentManager {
    contentEmbeds: { [id: string] : ContentEmbed } = {}
    embeds: { [embedKey:string]: EmbedData } = {};
    onEmbedsUpdated: Root extends Form ? (embeds: { [embedKey: string]: Array<EmbedData['value']> }) => any : null
    root: Form | Show;
    uniqueId = 0

    constructor(
        root: Root,
        embeds: ContentEmbedManager<Root>['embeds'] | null,
        config: {
            onEmbedsUpdated: ContentEmbedManager<Root>['onEmbedsUpdated']
        } = { onEmbedsUpdated: null }
    ) {
        super();
        this.root = root;
        this.embeds = embeds ?? {};
        this.onEmbedsUpdated = config.onEmbedsUpdated;
    }

    get serializedEmbeds() {
        return Object.values(this.contentEmbeds)
            .reduce((res, contentEmbed) => {
                return contentEmbed.removed ? res : {
                    ...res,
                    [contentEmbed.embed.key]: [...(res[contentEmbed.embed.key] ?? []), contentEmbed.value],
                }
            }, {});
    }

    newId(embed: EmbedData) {
        return `${embed.key}-${this.uniqueId++}`;
    }

    withEmbedsUniqueId<Content extends MaybeLocalizedContent>(content: Content): Content {
        if(!Object.values(this.embeds).length) {
            return content;
        }

        return this.maybeLocalized(content, content => {
            const contentDOM = new DOMParser().parseFromString(content, 'text/html');

            Object.values(this.embeds).forEach(embed => {
                contentDOM.querySelectorAll(embed.tag).forEach(element => {
                    element.setAttribute('data-unique-id', this.newId(embed));
                });
            });

            return contentDOM.body.innerHTML;
        });
    }

    serializeContent(content: string): string {
        if(!Object.values(this.embeds).length) {
            return content;
        }

        const contentDOM = new DOMParser().parseFromString(content, 'text/html');

        Object.values(this.embeds).forEach(embed => {
            contentDOM.querySelectorAll(embed.tag).forEach(element => {
                element.removeAttribute('data-unique-id');
                Object.values(embed.fields)
                    .forEach(embedField => {
                        if(embedField.type === 'upload') {
                            element.setAttribute(
                                hyphenate(embedField.key),
                                serializeUploadAttributeValue(parseAttributeValue(element.getAttribute(embedField.key)))
                            );
                        }
                    });
            });
        });

        return contentDOM.body.innerHTML;
    }

    async resolveContentEmbeds<Content extends MaybeLocalizedContent>(content: Content) {
        const { entityKey, instanceId } = this.root;
        const contentDOM = new DOMParser().parseFromString(this.allContent(content), 'text/html');

        this.contentEmbeds = {
            ...this.contentEmbeds,
            ...Object.fromEntries(
                Object.values(this.embeds)
                    .map(embed => {
                        const contentEmbeds = [...contentDOM.querySelectorAll(embed.tag)]
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

                        return contentEmbeds.map(contentEmbed => [contentEmbed.id, contentEmbed]);
                    })
                    .filter(Boolean)
                    .flat()
            ),
        }

        this.onEmbedsUpdated?.(this.serializedEmbeds);

        await Promise.allSettled(
            Object.values(this.embeds)
                .map(embed => (async () => {
                    const contentEmbeds = Object.values(this.contentEmbeds)
                        .filter(contentEmbed => contentEmbed.embed.key === embed.key);

                    if(!contentEmbeds.length) {
                        return;
                    }

                    const resolvedEmbeds = await api.post(
                        instanceId
                            ? route('code16.sharp.api.embed.instance.show', {
                                embedKey: embed.key,
                                entityKey,
                                instanceId
                            })
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
                })())
        );

        this.onEmbedsUpdated?.(this.serializedEmbeds);
    }

    getEmbedConfig(id: string): EmbedData|null {
        return this.contentEmbeds[id]?.embed;
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
                { ...this.contentEmbeds[id]?.value }
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
