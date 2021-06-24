import axios from 'axios';
import { api, apiUrl } from "sharp";
import { validateAutocompleteResponse } from "./util/autocomplete";

export function getAutocompleteSuggestions({
    url,
    method,
    locale,
    searchAttribute,
    query,
    dataWrapper,
    fieldKey,
    cancelToken,
}) {
    const isGet = method.toLowerCase() === 'get';
    const params = {
        locale,
        [searchAttribute]: query,
    };
    // use default request because it can be external API call
    return axios({
        url,
        method,
        params: isGet ? params : undefined,
        data: !isGet ? params : undefined,
        cancelToken,
    })
    .then(response => {
        if(!validateAutocompleteResponse({ results:response.data, dataWrapper, fieldKey, url })) {
            return [];
        }
        return dataWrapper
            ? response.data?.[dataWrapper] ?? []
            : response.data ?? [];
    });
}

export function downloadFileUrl({ entityKey, instanceId, fieldKey, fileName }) {
    return apiUrl(`form/download/${fieldKey}/${entityKey}/${instanceId}`, {
        params: {
            fileName,
        },
    })
}


export function getOriginalThumbnail({ path, disk }) {
    return api.get('/thumbnail', {
        params: {
            path,
            disk,
        }
    }).then(response => response.data.thumbnail);
}
