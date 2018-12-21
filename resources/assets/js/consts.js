import { getBaseUrl } from "./util";

export let BASE_URL = getBaseUrl();
export let API_PATH = `${BASE_URL}/api`;
export let UPLOAD_URL = `${API_PATH}/upload`;