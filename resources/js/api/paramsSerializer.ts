import qs from 'qs';

function transformValue(value) {
    if(Array.isArray(value) && !value.length) {
        // when value is an empty array send the param anyway (values[] => ?values&... )
        // used in «retained filters» to signify the filter has been resetted
        return null;
    }
    return value;
}

export function transformParams(params) {
    return Object.fromEntries(
        Object.entries(params ?? {}).map(([key, value]) => {
            return [key, transformValue(value)];
        })
    );
}

export default function paramsSerializer(params) {
    const transformedParams = transformParams(params);
    return qs.stringify(transformedParams, { strictNullHandling:true });
}
