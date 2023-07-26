import Vuex from "vuex";
import storeModule from './index';

let currentStore = null;

export function store() {
    if(!currentStore) {
        return currentStore = new Vuex.Store(storeModule);
    }
    return currentStore;
}