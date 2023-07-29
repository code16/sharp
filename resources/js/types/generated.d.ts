export type MenuData = {
  items: Array<MenuItemData>;
  userMenu: UserMenuData;
};
export type MenuItemData = {
  icon: string | null;
  label: string | null;
  url: string | null;
  isExternalLink: boolean;
  entityKey: string | null;
  isSeparator: boolean;
  current: boolean;
  children: Array<MenuItemData> | null;
  isCollapsible: boolean;
};
export type ThemeData = {
  menuLogoUrl: string | null;
};
export type UserMenuData = {
  items: Array<MenuItemData>;
};
