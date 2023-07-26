declare namespace Code16.Sharp.Data {
export type MenuData = {
items: Array<Code16.Sharp.Data.MenuItemData>;
logo: string | null;
hasGlobalFilters: boolean;
};
export type MenuItemData = {
icon: string | null;
label: string | null;
url: string | null;
isExternalLink: boolean;
entityKey: string | null;
isSeparator: boolean;
current: boolean;
children: Array<Code16.Sharp.Data.MenuItemData> | null;
isCollapsible: boolean;
};
}
