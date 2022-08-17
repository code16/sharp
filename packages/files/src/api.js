import { api, apiUrl } from 'sharp';


export function postResolveFiles({ entityKey, instanceId, files, thumbnailWidth, thumbnailHeight }) {
    return api.post(`/files/${entityKey}/${instanceId ?? ''}`, {
        files,
        thumbnail_width: thumbnailWidth,
        thumbnail_height: thumbnailHeight,
    })
    .then(response => response.data.files);
}

export function downloadFileUrl({ entityKey, instanceId, disk, path }) {
    return apiUrl(`/download/${entityKey}/${instanceId ?? ''}`, {
        params: {
            disk,
            path,
        },
    })
}
