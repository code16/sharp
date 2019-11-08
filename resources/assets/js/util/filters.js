
const filterQueryPrefix = 'filter_';
const filterQueryRE = new RegExp(`^${filterQueryPrefix}`);

export function filterQueryKey(key) {
    return `${filterQueryPrefix}${key}`;
}

export function getFiltersQueryParams(values, serialize = v=>v) {
    return Object.entries(values)
        .reduce((res, [key, value]) => ({
            ...res,
            [filterQueryKey(key)]: serialize(value, key),
        }), {});
}

export function getFiltersValuesFromQuery(query) {
    return Object.entries(query || {})
        .filter(([key]) => filterQueryRE.test(key))
        .reduce((res, [key, value]) => ({
            ...res,
            [key.replace(filterQueryRE, '')]: value
        }), {});
}