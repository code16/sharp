import { api } from "@/api";


export function getSearchResults({ query }) {
    return api.get('/search', { params: { q: query } })
        .then(response => response.data);
}
