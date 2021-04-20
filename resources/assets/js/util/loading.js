import { store } from "../store/store";


export function withLoadingOverlay(request) {
    store().dispatch('setLoading', true);
    return request.finally(() => {
        store().dispatch('setLoading', false);
    });
}
