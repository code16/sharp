import EntityList from "./entity-list/EntityList";
import Text from './Text';
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
    return map[type];
}