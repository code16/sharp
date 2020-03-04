
function getBaseUrl() {
    const meta = document.head.querySelector('meta[name=base-url]');
    return meta ? `/${meta.content}` : '/sharp';
}

export let BASE_URL = getBaseUrl();
export let API_PATH = `${BASE_URL}/api`;
export let UPLOAD_URL = `${API_PATH}/upload`;