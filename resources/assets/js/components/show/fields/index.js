import EntityList from "./entity-list/EntityList";

const map = {
    'entityList': EntityList,
};

export function getFieldByType(type) {
    return map[type];
}