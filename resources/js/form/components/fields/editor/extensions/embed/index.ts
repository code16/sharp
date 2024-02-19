import { reactive } from "vue";
import { Embed } from "./embed";
import debounce from "lodash/debounce";
import { api } from "@/api";
import { route } from "@/utils/url";
import { EmbedData, FormData } from "@/types";
import { Form } from "@/form/Form";

export type EmbedOptions = {
    embed: EmbedData,
    isReady: () => boolean,
    getEmbed: (id: number) => any,
    getAdditionalData: (attrs: any) => Promise<any>,
    resolveForm: (attrs: any) => Promise<any>,
    postForm: (data: FormData['data'], form: Form) => Promise<any>,
}

export function getEmbedExtension({
    embed,
    entityKey,
    instanceId,
    onUpdated
}: {
    embed: EmbedData,
    entityKey: string,
    instanceId: string|number,
    onUpdated: (responseData: FormData['data'], form: Form) => void,
}) {
    const state = reactive({
        embeds: [],
        currentIndex: 0,
        created: false,
        resolved: null,
        onResolve: null,
    });

    state.resolved = new Promise(resolve => state.onResolve = resolve);

    const resolveEmbeds = embeds => {
        return api
            .post(
                instanceId
                    ? route('code16.sharp.api.embed.instance.show', { embedKey: embed.key, entityKey, instanceId })
                    : route('code16.sharp.api.embed.show', { embedKey: embed.key, entityKey }),
                { embeds, form: true }
            )
            .then(response => response.data.embeds);
    }

    const config = {
        name: `embed:${embed.key}`,
        onCreate: debounce(async () => {
            if(state.currentIndex > 0) {
                state.embeds = await resolveEmbeds(state.embeds);
                state.onResolve();
            }
            state.created = true;
        }),
    }

    const options: EmbedOptions = {
        embed,
        isReady: () => {
            return state.created;
        },
        getEmbed: id => {
            return state.embeds[id];
        },
        async getAdditionalData(attrs) {
            if(state.created) {
                return null;
            }
            const index = state.currentIndex++;
            state.embeds.push(attrs);
            await state.resolved;
            return state.embeds[index];
        },
        resolveForm(attributes) {
            return api
                .post(
                    instanceId
                        ? route('code16.sharp.api.embed.instance.form.show', { embedKey: embed.key, entityKey, instanceId })
                        : route('code16.sharp.api.embed.form.show', { embedKey: embed.key, entityKey }),
                    { ...attributes }
                )
                .then(response => response.data);
        },
        postForm(data: FormData['data'], form: Form) {
            return api
                .post(
                    instanceId
                        ? route('code16.sharp.api.embed.instance.form.update', { embedKey: embed.key, entityKey, instanceId })
                        : route('code16.sharp.api.embed.form.update', { embedKey: embed.key, entityKey }),
                    { ...data }
                )
                .then(response => {
                    onUpdated(response.data, form);
                    return response.data;
                });
        },
    }

    return Embed
        .extend(config)
        .configure(options);
}
