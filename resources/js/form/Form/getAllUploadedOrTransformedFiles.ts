import { FormData, FormUploadFieldValueData } from "@/types";
import { Form } from "@/form/Form";


export function getAllUploadedOrTransformedFiles(this: Form, postResponseData: FormData['data']):
{ [fieldKey: string]: FormUploadFieldValueData | { [fieldKey: string]: FormUploadFieldValueData }[] } {
    const getFiles = (data, responseData, fields: FormData['fields']) => {
        return Object.fromEntries(
            Object.entries(fields)
                .map(([fieldKey, field]) => {
                    if(field.type === 'list') {
                        return [
                            field.key,
                            data[field.key].map((item, i) =>
                                getFiles(item, responseData[field.key]?.[i], field.itemFields)
                            )
                        ];
                    }
                    if(field.type === 'upload') {
                        return [
                            field.key,
                            {
                                ...data[field.key],
                                ...responseData[field.key],
                            }
                        ];
                    }
                    return null;
                })
                .filter(Boolean)
        );
    }
    return getFiles(this.data, postResponseData, this.fields)
}
