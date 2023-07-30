export type BreadcrumbData = {
  items: Array<BreadcrumbItemData>;
  visible: boolean;
};
export type BreadcrumbItemData = {
  type: string;
  name: string;
  documentTitleLabel: string;
  entityKey: string;
  url: string;
};
export type CheckFilterData = {
  key: string;
  label: string;
  type: FilterType;
  default: boolean;
};
export type CommandData = {
  key: string;
  label: string | null;
  description: string | null;
  type: CommandType;
  confirmation: string | null;
  modal_title: string | null;
  modal_confirm_label: string | null;
  has_form: boolean;
  authorization: Array<string | number> | boolean;
  instance_selection?: InstanceSelectionMode | null;
};
export type CommandType = "dashboard" | "entity" | "instance";
export type DashboardData = {};
export type DateRangeFilterData = {
  key: string;
  label: string;
  type: FilterType;
  default: DateRangeFilterValueData | null;
  required: boolean;
  mondayFirst: boolean;
  displayFormat: string;
};
export type DateRangeFilterValueData = {
  start: string;
  end: string;
};
export type EntityListAuthorizationsData = {
  view: Array<number | string>;
  update: Array<number | string>;
  create: boolean;
};
export type EntityListConfigData = {
  instanceIdAttribute: string;
  multiformAttribute: string | null;
  searchable: boolean;
  paginated: boolean;
  reorderable: boolean;
  defaultSort: string | null;
  defaultSortDir: string | null;
  hasShowPage: boolean;
  deleteConfirmationText: string;
  deleteHidden: boolean;
  filters: Array<Array<FilterData>>;
  commands: Record<CommandType, Array<Array<CommandData>>>;
  state: EntityStateData;
  globalMessage?: Array<any> | null;
};
export type EntityListData = {
  containers: { [key: string]: EntityListFieldData };
  layout: Array<EntityListFieldLayoutData>;
  data: Array<{ [key: string]: any }>;
  fields: { [key: string]: any };
  config: EntityListConfigData;
  forms: Array<EntityListMultiformData>;
  notifications: Array<NotificationData>;
  breadcrumb: BreadcrumbData;
  authorizations: EntityListAuthorizationsData;
};
export type EntityListFieldData = {
  key: string;
  label: string;
  sortable: boolean;
  html: boolean;
};
export type EntityListFieldLayoutData = {
  key: string;
  size: string;
  hideOnXS: boolean;
  sizeXS: string;
};
export type EntityListMultiformData = {
  key: string;
  label: string;
  instances: Array<number | string>;
};
export type EntityStateData = {
  attribute: string;
  values: Array<EntityStateValueData>;
  authorization: boolean | Array<string | number> | null;
};
export type EntityStateValueData = {
  value: string | number;
  label: string;
  color: string;
};
export type FilterData = {
  key: string;
  label: string;
  type: FilterType;
  default: any;
};
export type FilterType = "select" | "daterange" | "check";
export type FormData = {};
export type InstanceSelectionMode = "required" | "allowed";
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
export type NotificationData = {
  title: string;
  level: NotificationLevel;
  message: string | null;
  autoHide: boolean;
};
export type NotificationLevel = "info" | "success" | "warning" | "danger";
export type SelectFilterData = {
  key: string;
  label: string;
  type: FilterType;
  default: number | string | Array<number | string> | null;
  multiple: boolean;
  required: boolean;
  values: Array<SelectFilterValueData>;
  master: boolean;
  searchable: boolean;
  searchKeys: Array<any>;
  template: string;
};
export type SelectFilterValueData = {
  id: string | number;
  label: string;
};
export type ShowData = {};
export type ThemeData = {
  menuLogoUrl: string | null;
};
export type UserMenuData = {
  items: Array<MenuItemData>;
};
