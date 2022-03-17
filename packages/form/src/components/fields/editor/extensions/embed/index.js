import Vue from "vue";
import { Embed } from "./embed";
import { postEmbedForm, postResolveEmbedForm, postResolveEmbeds } from "sharp-embeds";
import debounce from "lodash/debounce";



export function getEmbedExtension({
    embedKey,
    props,
    form,
}) {

    const state = Vue.observable({
        embeds: [],
        currentIndex: 0,
        created: false,
        resolved: null,
        onResolve: null,
    });

    state.resolved = new Promise(resolve => state.onResolve = resolve);

    const resolveEmbeds = embeds => {
        return postResolveEmbeds({
            entityKey: form.entityKey,
            instanceId: form.instanceId,
            embedKey,
            embeds,
        })
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
        label: props.label,
        tag: props.tag,
        attributes: props.attributes ?? [],
        template: props.previewTemplate,
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
            return postResolveEmbedForm({
                entityKey: form.entityKey,
                instanceId: form.instanceId,
                embedKey,
                attributes,
            });
        },
        postForm(data) {
            return postEmbedForm({
                entityKey: form.entityKey,
                instanceId: form.instanceId,
                embedKey,
                data,
            });
        },
    }

    return Embed
        .extend(config)
        .configure(options);
}
