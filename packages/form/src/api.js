import axios from 'axios';
import { apiUrl } from "sharp";
import { validateAutocompleteResponse } from "./util/autocomplete";

export function getAutocompleteSuggestions({ url, method, locale, searchAttribute, query, dataWrapper, fieldKey, cancelToken, }) {
    const isGet = method.toLowerCase() === 'get';
    const params = {
        locale,
        [searchAttribute]: query,
    };
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
