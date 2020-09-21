import axios from 'axios';
import { apiUrl, logError } from "sharp";

export function getAutocompleteSuggestions({ url, method, locale, searchAttribute, query, dataWrapper, fieldKey }) {
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
    })
    .then(response => {
        if (dataWrapper && !(dataWrapper in response.data)) {
            logError(`Autocomplete (${fieldKey}): dataWrapper "${dataWrapper}" seems to be invalid :`);
            logError(`- search url "${url}"`);
            logError(`- results`, response.data);
            return [];
        }
        if(!dataWrapper && response.data && !Array.isArray(response.data)) {
            logError(`Autocomplete (${fieldKey}): search results response is not an array, please use setDataWrapper() if results are wrapped inside an object (https://sharp.code16.fr/docs/guide/form-fields/autocomplete.html#setdatawrapper)`);
            logError(`- search url "${url}"`);
            logError(`- response`, response.data);
            return [];
        }
        return dataWrapper ? response.data[dataWrapper] : response.data
    });
}

export function downloadFileUrl({ entityKey, instanceId, fieldKey, fileName }) {
    return apiUrl(`form/download/${fieldKey}/${entityKey}/${instanceId}`, {
        params: {
            fileName,
        },
    })
}