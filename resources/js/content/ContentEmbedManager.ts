import { Form } from "@/form/Form";
import { EmbedData, FormData, FormEditorFieldData } from "@/types";
import { api } from "@/api/api";
import { route } from "@/utils/url";
import { Show } from "@/show/Show";
import { reactive, watch } from "vue";

type ContentEmbed = {
    embed: EmbedData,
    removed?: boolean,
    value: EmbedData['value'],
    locale?: string,
}

export class ContentEmbedManager<Root extends Form | Show> {
    embeds: { [embedKey:string]: EmbedData } = {};
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
    ) {
        this.root = root;
        this.embeds = embeds ?? {};
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

    getEmbed(embed: EmbedData, id: string): EmbedData["value"] {
        return this.contentEmbeds[embed.key]?.[id]?.value;
    }

    newEmbed(embed: EmbedData, locale: string | null, value?: EmbedData['value']): string {
        const id = String(Object.keys(this.contentEmbeds[embed.key] ?? {}).length);
        this.contentEmbeds[embed.key] ??= {};
        this.contentEmbeds[embed.key][id] = {
            embed,
            value: value ? { ...value, _locale: locale } : null,
        };
        return id;
    }

    syncEmbeds(embed: EmbedData, locale: string | null, ids: (string | number)[]) {
        this.contentEmbeds[embed.key] = {
            ...Object.fromEntries(
                Object.entries(this.contentEmbeds[embed.key] ?? {})
                    .map(([id, contentEmbed]) => [
                        id,
                        ({
                            ...contentEmbed,
                            removed: contentEmbed.value?._locale == locale
                                ? !ids.some(i => String(i) === id)
                                : contentEmbed.removed,
                        })
                    ])
            ),
        };
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

    async postForm(id: string, embed: EmbedData, locale: string | null, data: EmbedData['value']): Promise<{ id:string }> {
        const { entityKey, instanceId } = this.root;
        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                { ...data }
            )
            .then(response => response.data);

        id ??= this.newEmbed(embed, locale);

        this.contentEmbeds[embed.key][id] = {
            embed,
            value: {
                ...responseData,
                _locale: locale,
            },
        }

        return {
            id
        };
    }
}
