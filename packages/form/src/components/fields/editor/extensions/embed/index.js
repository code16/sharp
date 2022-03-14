import Vue from "vue";
import { Embed } from "./embed";
import { postResolveEmbeds } from "sharp-embeds";
import { filesEquals } from "sharp-files";


export function getEmbedExtension({
    embedKey,
    props,
    form,
}) {

    const state = Vue.observable({
        created: false,
        embeds: {},
        currentId: 0,
    });

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
        onCreate: async () => {
            if(state.embeds.length > 0) {
                state.embeds = {
                    ...await resolveEmbeds(state.embeds)
                };
            }
            state.created = true;
        },
    }

    const options = {
        tag: props.tag,
        attributes: props.attributes,
        template: props.template,
        isReady: () => {
            return state.created;
        },
        getEmbed: id => {
            return state.embeds[id];
        },
        registerEmbed: async (attrs) => {
            const id = state.currentId++;
            if(state.created) {
                // const embeds = await resolveEmbeds([attrs]);
                // state.embeds = {
                //     ...state.embeds,
                //     [id]: embeds[0],
                // };
            } else {
                state.embeds = {
                    ...state.embeds,
                    [id]: attrs,
                };
            }
            return id;
        },
        onUpdate: (id, data) => {
            state.embeds = {
                ...state.embeds,
                [id]: data,
            }
        },
    }

    return Embed
        .extend(config)
        .configure(options);
}
