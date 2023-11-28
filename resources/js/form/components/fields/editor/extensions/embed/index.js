import { reactive } from "vue";
import { Embed } from "./embed";
import debounce from "lodash/debounce";
import { api } from "@/api";
import { route } from "@/utils/url";


export function getEmbedExtension({
    embedKey,
    embedOptions,
    entityKey,
    instanceId,
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
                    ? route('code16.sharp.api.embed.instance.show', { embedKey, entityKey, instanceId })
                    : route('code16.sharp.api.embed.show', { embedKey, entityKey }),
                { embeds, form: true }
            )
            .then(response => response.data.embeds);
    }

    const config = {
        name: `embed:${embedKey}`,
        onCreate: debounce(async () => {
            if(state.currentIndex > 0) {
                state.embeds = await resolveEmbeds(state.embeds);
                state.onResolve();
            }
            state.created = true;
        }),
    }

    const options = {
        label: embedOptions.label,
        tag: embedOptions.tag,
        attributes: embedOptions.attributes ?? [],
        template: embedOptions.template,
        state,
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
        onUpdate: (id, data) => {
            state.embeds = {
                ...state.embeds,
                [id]: data,
            }
        },
        onRemove: (id) => {
            const { [id]:removedEmbed, ...embed } = state.embeds;
            state.embeds = embed;
        },
        resolveForm(attributes) {
            return api
                .post(
                    instanceId
                        ? route('code16.sharp.api.embed.instance.form.show', { embedKey, entityKey, instanceId })
                        : route('code16.sharp.api.embed.form.show', { embedKey, entityKey }),
                    { ...attributes }
                )
                .then(response => response.data);
        },
        postForm(data) {
            return api
                .post(
                    instanceId
                        ? route('code16.sharp.api.embed.instance.form.update', { embedKey, entityKey, instanceId })
                        : route('code16.sharp.api.embed.form.update', { embedKey, entityKey }),
                    { ...data }
                )
                .then(response => response.data);
        },
    }

    return Embed
        .extend(config)
        .configure(options);
}
