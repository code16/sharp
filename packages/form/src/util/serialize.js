import { serializeEditorPostValue } from "../components/fields/editor/util/serialize";


function serializeValue(value, fieldOptions) {
    if(fieldOptions?.type === 'editor') {
        return serializeEditorPostValue(value);
    }

    return value;
}

export function serializePostData(data, fields) {
    return Object.fromEntries(
        Object.entries(data ?? {})
            .filter(([key]) => fields[key]?.type !== 'html')
            .map(([key, value]) => [
                key,
                serializeValue(value, fields[key]),
            ])
    );
}
