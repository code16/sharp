import { Form } from "@/form/Form";
import { EmbedData, FormData, FormEditorFieldData } from "@/types";
import { api } from "@/api/api";
import { route } from "@/utils/url";
import { Show } from "@/show/Show";
import { reactive } from "vue";

type ContentEmbed = {
    embed: EmbedData,
    removed?: boolean,
    value: EmbedData['value'],
}

export class ContentEmbedManager<Root extends Form | Show> {
    embeds: { [embedKey:string]: EmbedData } = {};
    onEmbedsUpdated: Root extends Form ? (embeds: FormEditorFieldData['value']['embeds']) => any : null
    root: Form | Show;

    state = reactive<{
        contentEmbeds: {
            [embedKey: string]: {
                [id: string] : ContentEmbed,
            },
        }
    }>({
        contentEmbeds: {}
    });

    constructor(
        root: Root,
        embeds: ContentEmbedManager<Root>['embeds'] | null,
        initialEmbeds: FormEditorFieldData['value']['embeds'],
        config: {
            onEmbedsUpdated: ContentEmbedManager<Root>['onEmbedsUpdated']
        } = { onEmbedsUpdated: null }
    ) {
        this.root = root;
        this.embeds = embeds ?? {};
        this.onEmbedsUpdated = config.onEmbedsUpdated;
        this.contentEmbeds = Object.fromEntries(
            Object.entries(initialEmbeds ?? {}).map(([embedKey, embeds]) =>
                [embedKey, Object.fromEntries(
                    Object.entries(embeds).map(([id, value]) =>
                        [id, { embed: this.embeds[embedKey], value }]
                    )
                )]
            )
        );
    }

    get contentEmbeds() {
        return this.state.contentEmbeds;
    }

    set contentEmbeds(contentEmbeds: { [embedKey: string]: { [id: string]: ContentEmbed } }) {
        this.state.contentEmbeds = contentEmbeds;
    }

    get serializedEmbeds(): FormEditorFieldData['value']['embeds'] {
        return Object.fromEntries(
            Object.entries(this.contentEmbeds)
                .map(([embedKey, embeds]) => [
                    embedKey,
                    Object.fromEntries(
                        Object.entries(embeds)
                            .filter(([id, contentEmbed]) => !contentEmbed.removed)
                            .map(([id, contentEmbed]) => [id, contentEmbed.value])
                    )
                ])
        );
    }

    getEmbed(id: string, embed: EmbedData): EmbedData['value'] {
        return this.contentEmbeds[embed.key][id].value;
    }

    newEmbed(embed: EmbedData): string {
        const id = String(Object.keys(this.contentEmbeds[embed.key] ?? {}).length);
        this.contentEmbeds[embed.key] ??= {};
        this.contentEmbeds[embed.key][id] = {
            embed,
            value: null,
        };
        return id;
    }

    restoreEmbed(id: string, embed: EmbedData) {
        this.contentEmbeds[embed.key][id] = {
            ...this.contentEmbeds[embed.key][id],
            removed: false,
        }
        this.onEmbedsUpdated(this.serializedEmbeds);
    }

    removeEmbed(id: string, embed: EmbedData) {
        this.contentEmbeds[embed.key][id] = {
            ...this.contentEmbeds[embed.key][id],
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
                { ...this.contentEmbeds[embed.key]?.[id]?.value }
            )
            .then(response => response.data);
    }

    async postForm(id: string, embed: EmbedData, data: EmbedData['value']): Promise<{ id:string }> {
        const { entityKey, instanceId } = this.root;
        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                { ...data }
            )
            .then(response => response.data);

        id ??= this.newEmbed(embed);

        this.contentEmbeds[embed.key][id] = {
            embed,
            value: responseData,
        }

        this.onEmbedsUpdated(this.serializedEmbeds);

        return {
            id
        };
    }
}
