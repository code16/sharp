import { isCustomField, resolveCustomField } from "sharp";
import EntityList from "./entity-list/EntityList.vue";
import Text from './text/Text.vue';
import Picture from './Picture.vue';
import File from './File.vue';
import List from './List.vue';

const map = {
    'entityList': EntityList,
    'text': Text,
    'picture': Picture,
    'file': File,
    'list': List,
};

export function getFieldByType(type) {
    if(isCustomField(type)) {
        return resolveCustomField(type);
    }
    return map[type];
}
