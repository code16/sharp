import { api } from 'sharp';


export function postResolveEmbeds({ entityKey, instanceId, embedKey, embeds }) {
    return api.post(`/embeds/${embedKey}/${entityKey}/${instanceId ?? ''}`, {
        embeds,
    })
    .then(response => response.data.embeds);
}
