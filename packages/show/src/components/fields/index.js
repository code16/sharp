import EntityList from "./entity-list/EntityList";
import Text from './Text';
import Picture from './Picture';


const map = {
    'entityList': EntityList,
    'text': Text,
    'picture': Picture,
};

export function getFieldByType(type) {
    return map[type];
}