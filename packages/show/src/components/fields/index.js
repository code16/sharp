import { isCustomField, resolveCustomField } from "sharp";
import EntityList from "./entity-list/EntityList";
import Text from './text/Text';
import Picture from './Picture';
import File from './File';
import List from './List';

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
