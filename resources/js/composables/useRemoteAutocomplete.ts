import { AxiosError, AxiosResponse, isCancel } from "axios";
import { Ref, ref } from "vue";


export function useRemoteAutocomplete<T extends any[] = any[]>(
    post: (props: ReturnType<typeof postProps>) => Promise<T>,
    options: {
        minLength: number;
        debounceDelay: number;
    }
) {
    let abortController: AbortController | null = null;
    let timeout = null;
    let loadingTimeout = null;
    const results = ref([]);
    const loading = ref(false);

    function postProps(query: string) {
        return {
            query,
            signal: abortController.signal,
            onSuccess: (response: AxiosResponse) => {
                clearTimeout(loadingTimeout);
                loading.value = false;
                return response;
            },
            onError: (e: AxiosError) => {
                if(isCancel(e)) {
                    clearTimeout(loadingTimeout);
                }
                return Promise.reject(e);
            }
        }
    }

    function search(query: string) {
        clearTimeout(loadingTimeout);
        loadingTimeout = setTimeout(() => {
            loading.value = true;
        }, 200);
        abortController?.abort();
        abortController = new AbortController();

        return post(postProps(query))
            .then(r => results.value = r);
    }

    return {
        results,
        loading,
        async search(query: string, immediate?: boolean) {
            return new Promise<T>((resolve, reject) => {
                clearTimeout(timeout);
                if(query.length >= options.minLength) {
                    if(!results.value.length) {
                        loading.value = true;
                    }
                    if(immediate) {
                        search(query).then(resolve, reject)
                    } else {
                        timeout = setTimeout(() => search(query).then(resolve, reject), options.debounceDelay)
                    }
                } else {
                    clearTimeout(timeout);
                    loading.value = false;
                    results.value = [];
                    resolve([] as T);
                }
            });
        }
    }
}
