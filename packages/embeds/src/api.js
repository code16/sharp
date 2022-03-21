import { api } from 'sharp';


export function postResolveEmbeds({ entityKey, instanceId, embedKey, embeds, form=false }) {
    return api.post(`/embeds/${embedKey}/${entityKey}/${instanceId ?? ''}`, {
        embeds,
        form,
    })
    .then(response => response.data.embeds);
}

export function postResolveEmbedForm({ entityKey, instanceId, embedKey, attributes }) {
    return api.post(`/embeds/${embedKey}/${entityKey}${instanceId ? `/${instanceId}` : ''}/form/init`, {
        ...attributes,
    })
    .then(response => response.data);
}

export function postEmbedForm({ entityKey, instanceId, embedKey, data }) {
    return api.post(`/embeds/${embedKey}/${entityKey}${instanceId ? `/${instanceId}` : ''}/form`, {
        ...data,
    })
    .then(response => response.data);
}
