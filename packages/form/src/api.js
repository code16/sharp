import axios from 'axios';

export function getAutocompleteSuggestions({ url, method, locale, searchAttribute, query, }) {
    const params = {
        locale,
        [searchAttribute]: query,
    };
    if(method.toLowerCase() === 'get') {
        return axios.get(url, { params})
            .then(response => response.data);
    } else {
        return axios.post(url, params)
            .then(response => response.data);
    }
}