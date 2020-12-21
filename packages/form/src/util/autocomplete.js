import {logError} from "sharp";

export function validateAutocompleteResponse({ results, dataWrapper, fieldKey, url }) {
    if(results) {
        if (dataWrapper && !results.hasOwnProperty(dataWrapper)) {
            logError(`Autocomplete (${fieldKey}): dataWrapper "${dataWrapper}" seems to be invalid :`);
            logError(`- search url "${url}"`);
            logError(`- results`, results);
            return false;
        }
        if(!dataWrapper && !Array.isArray(results)) {
            logError(`Autocomplete (${fieldKey}): search results response is not an array, please use setDataWrapper() if results are wrapped inside an object (https://sharp.code16.fr/docs/guide/form-fields/autocomplete.html#setdatawrapper)`);
            logError(`- search url "${url}"`);
            logError(`- response`, results);
            return false;
        }
    }
    return true;
}