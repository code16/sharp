export type BreadcrumbData = {
  items: Array<BreadcrumbItemData>;
};
export type BreadcrumbItemData = {
  type: string;
  name: string;
  documentTitleLabel: string;
  entityKey: string;
  url: string;
};
export type CheckFilterData = {
  value?: boolean;
  key: string;
  label: string;
  type: "check";
  default: boolean | null;
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
  primary?: boolean | null;
  instance_selection?: InstanceSelectionMode | null;
};
export type CommandType = "dashboard" | "entity" | "instance";
export type ConfigCommandsData = Record<
  CommandType | string,
  Array<Array<CommandData>>
>;
export type ConfigFiltersData = { _root: Array<FilterData> } & {
  [key: string]: Array<FilterData>;
};
export type DashboardConfigData = {
  commands?: ConfigCommandsData | null;
  filters?: ConfigFiltersData | null;
  globalMessage?: PageAlertConfigData | null;
};
export type DashboardData = {
  widgets: Array<WidgetData>;
  config: DashboardConfigData;
  layout: DashboardLayoutData;
  data: { [key: string]: any };
  fields: { [key: string]: any };
};
export type DashboardLayoutData = {
  sections: Array<DashboardLayoutSectionData>;
};
export type DashboardLayoutSectionData = {
  key: string | null;
  title: string;
  rows: Array<Array<DashboardLayoutWidgetData>>;
};
export type DashboardLayoutWidgetData = {
  size: number;
  key: string;
};
export type DateRangeFilterData = {
  value?: DateRangeFilterValueData | null;
  key: string;
  label: string;
  type: "daterange";
  default: DateRangeFilterValueData | null;
  required: boolean;
  mondayFirst: boolean;
  displayFormat: string;
};
export type DateRangeFilterValueData = {
  start: string;
  end: string;
};
export type EmbedData = {
  key: string;
  label: string;
  tag: string;
  attributes: Array<string>;
  template: string;
};
export type EntityAuthorizationsData = {
  view: Array<number | string>;
  update: Array<number | string>;
  create: boolean;
};
export type EntityListConfigData = {
  instanceIdAttribute: string;
  searchable: boolean;
  paginated: boolean;
  reorderable: boolean;
  defaultSort: string | null;
  defaultSortDir: string | null;
  hasShowPage: boolean;
  deleteConfirmationText: string;
  deleteHidden: boolean;
  filters?: ConfigFiltersData | null;
  commands?: ConfigCommandsData | null;
  multiformAttribute?: string | null;
  state?: EntityStateData | null;
  globalMessage?: PageAlertConfigData | null;
};
export type EntityListData = {
  containers: { [key: string]: EntityListFieldData };
  layout: Array<EntityListFieldLayoutData>;
  data: { list: Array<{ [key: string]: any }> } & {
    [key: string]: ShowHtmlFieldData;
  };
  fields: { [key: string]: any };
  config: EntityListConfigData;
  forms: Array<EntityListMultiformData>;
  authorizations: EntityAuthorizationsData;
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
export type FigureWidgetData = {
  value?: {
    key: string;
    data: {
      figure: string;
      unit: string;
      evolution: { increase: boolean; value: string };
    };
  };
  key: string;
  type: WidgetType;
  title: string | null;
  link: string | null;
};
export type FilterData =
  | CheckFilterData
  | DateRangeFilterData
  | SelectFilterData;
export type FilterType = "select" | "daterange" | "check";
export type FormConfigData = {
  hasShowPage: boolean;
  deleteConfirmationText: string | null;
  isSingle: boolean;
  breadcrumbAttribute?: string | null;
  globalMessage?: PageAlertConfigData | null;
};
export type FormData = {
  config: FormConfigData;
};
export type FormLayoutColumnData = {
  tabbed: boolean;
};
export type FormLayoutData = {
  tabbed: boolean;
  tabs: Array<FormLayoutTabData>;
};
export type FormLayoutTabData = {
  title: string;
  columns: Array<FormLayoutColumnData>;
};
export type GlobalFiltersData = {
  filters: ConfigFiltersData;
};
export type GraphWidgetData = {
  value?: {
    key: string;
    datasets: Array<{ label: string; data: number[]; color: string }>;
    labels: string[];
  };
  key: string;
  type: WidgetType;
  title: string | null;
  display: GraphWidgetDisplay;
  showLegend: boolean;
  minimal: boolean;
  ratioX: number | null;
  ratioY: number | null;
  height: number | null;
  dateLabels: boolean;
  options?: { curved: boolean; horizontal: boolean };
};
export type GraphWidgetDisplay = "bar" | "line" | "pie";
export type InstanceAuthorizationsData = {
  view: boolean;
  create: boolean;
  update: boolean;
  delete: boolean;
};
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
export type OrderedListWidgetData = {
  value?: { key: string; data: Array<{ label: string; url?: string }> };
  key: string;
  type: WidgetType;
  title: string | null;
  link: string | null;
  html: boolean;
};
export type PageAlertConfigData = {
  fieldKey: string;
  alertLevel: PageAlertLevel | null;
};
export type PageAlertLevel =
  | "info"
  | "warning"
  | "danger"
  | "primary"
  | "secondary";
export type PanelWidgetData = {
  value?: { key: string; data: { [key: string]: any } };
  key: string;
  type: WidgetType;
  title: string | null;
  link: string | null;
  template: string;
};
export type SelectFilterData = {
  value?: number | string | Array<number | string> | null;
  key: string;
  label: string | null;
  type: "select";
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
export type ShowConfigData = {
  deleteConfirmationText: string;
  isSingle: boolean;
  commands?: Record<CommandType, Array<Array<CommandData>>>;
  multiformAttribute?: string | null;
  titleAttribute?: string | null;
  breadcrumbAttribute?: string | null;
  state?: EntityStateData | null;
  globalMessage?: PageAlertConfigData | null;
};
export type ShowData = {
  config: ShowConfigData;
  fields: { [key: string]: ShowFieldData };
  layout: ShowLayoutData;
  data: { [key: string]: any };
  locales: Array<string> | null;
  authorizations: InstanceAuthorizationsData;
};
export type ShowEntityListFieldData = {
  key: string;
  type: ShowFieldType;
  emptyVisible: boolean;
  entityListKey: string;
  hiddenFilters: Array<string>;
  hiddenCommands: Array<string>;
  showEntityState: boolean;
  showReorderButton: boolean;
  showCreateButton: boolean;
  showSearchField: boolean;
  showCount: boolean;
  label: string | null;
};
export type ShowFieldData = {
  key: string;
  type: ShowFieldType;
  emptyVisible: boolean;
};
export type ShowFieldType =
  | "file"
  | "html"
  | "list"
  | "picture"
  | "text"
  | "entityList";
export type ShowFileFieldData = {
  key: string;
  type: ShowFieldType;
  emptyVisible: boolean;
  label: string | null;
};
export type ShowHtmlFieldData = {
  key: string;
  type: ShowFieldType;
  emptyVisible: boolean;
  template: string;
  templateData: { [key: string]: any } | null;
};
export type ShowLayoutColumnData = {
  size: number;
  fields: Array<ShowLayoutFieldData>;
};
export type ShowLayoutData = {
  sections: Array<ShowLayoutSectionData>;
};
export type ShowLayoutFieldData = {
  key: string;
  size: number;
  sizeXS: number;
  item?: { [key: string]: ShowLayoutFieldData } | null;
};
export type ShowLayoutSectionData = {
  key: string | null;
  title: string;
  collapsable: boolean;
  columns: Array<ShowLayoutColumnData>;
};
export type ShowListFieldData = {
  key: string;
  type: ShowFieldType;
  emptyVisible: boolean;
  label: string | null;
  itemFields: { [key: string]: ShowFieldData };
};
export type ShowPictureFieldData = {
  key: string;
  type: ShowFieldType;
  emptyVisible: boolean;
};
export type ShowTextFieldData = {
  key: string;
  type: ShowFieldType;
  emptyVisible: boolean;
  html: boolean;
  localized: boolean | null;
  collapseToWordCount: number | null;
  embeds: { [key: string]: EmbedData } | null;
  label: string | null;
};
export type ThemeData = {
  loginLogoUrl: string | null;
  menuLogoUrl: string | null;
};
export type UserData = {
  name: string | null;
};
export type UserMenuData = {
  items: Array<MenuItemData>;
};
export type WidgetData = {
  key: string;
  type: WidgetType;
  title: string | null;
  link: string | null;
};
export type WidgetType = "figure" | "graph" | "list" | "panel";
