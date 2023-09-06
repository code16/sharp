import {
    EntityAuthorizationsData,
    EntityListConfigData,
    EntityListData,
    EntityListFieldData, EntityListFieldLayoutData, EntityListMultiformData,
    ShowHtmlFieldData
} from "@/types";


export class EntityList implements EntityListData {
    authorizations: EntityAuthorizationsData;
    config: EntityListConfigData;
    containers: { [p: string]: EntityListFieldData };
    data: { list: Array<{ [p: string]: any }> } & { [p: string]: ShowHtmlFieldData };
    fields: { [p: string]: any };
    forms: Array<EntityListMultiformData>;
    layout: Array<EntityListFieldLayoutData>;



}
