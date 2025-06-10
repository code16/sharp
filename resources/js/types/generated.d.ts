export type AutocompleteRemoteFilterData = {
  value?: { id: string | number; label: string };
  key: string;
  label: string | null;
  type: "autocompleteRemote";
  required: boolean;
  master: boolean;
  debounceDelay: number;
  searchMinChars: number;
};
export type BreadcrumbData = {
  items: Array<BreadcrumbItemData>;
};
export type BreadcrumbItemData = {
  type: string;
  label: string;
  documentTitleLabel: string | null;
  entityKey: string;
  url: string;
};
export type CheckFilterData = {
  value?: boolean;
  key: string;
  label: string | null;
  type: "check";
};
export type CommandAction =
  | "download"
  | "info"
  | "link"
  | "reload"
  | "refresh"
  | "step"
  | "streamDownload"
  | "view";
export type CommandData = {
  key: string;
  label: string | null;
  description: string | null;
  type: CommandType;
  confirmation: {
    text: string;
    title: string | null;
    buttonLabel: string | null;
  } | null;
  hasForm: boolean;
  authorization: Array<string | number> | boolean;
  instanceSelection: InstanceSelectionMode | null;
  primary: boolean | null;
};
export type CommandFormConfigData = {
  title: string | null;
  description: string | null;
  buttonLabel: string | null;
  showSubmitAndReopenButton: boolean;
  submitAndReopenButtonLabel: string | null;
};
export type CommandFormData = {
  data: { [key: string]: FormFieldData["value"] };
  config: CommandFormConfigData;
  fields: { [key: string]: FormFieldData } | null;
  layout: FormLayoutData | null;
  locales: Array<string> | null;
  pageAlert: PageAlertData | null;
};
export type CommandResponseData =
  | { action: "link"; link: string }
  | { action: "info"; message: string; reload: boolean }
  | { action: "refresh"; items?: Array<{ [key: string]: any }> }
  | { action: "reload" }
  | { action: "step"; step: string }
  | { action: "view"; html: string };
export type CommandType = "dashboard" | "entity" | "instance";
export type ConfigCommandsData = Record<
  CommandType | string,
  Array<Array<CommandData>>
>;
export type ConfigFiltersData = { _root: Array<FilterData> } & {
  [key: string]: Array<FilterData>;
};
export type DashboardConfigData = {
  commands: ConfigCommandsData | null;
  filters: ConfigFiltersData | null;
};
export type DashboardData = {
  widgets: Array<WidgetData>;
  config: DashboardConfigData;
  layout: DashboardLayoutData;
  data: { [key: string]: any };
  filterValues: FilterValuesData;
  pageAlert: PageAlertData | null;
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
  value?: {
    start: string;
    end: string;
    formatted?: { start: string; end: string };
    preset?: string;
  } | null;
  key: string;
  label: string | null;
  type: "daterange";
  required: boolean;
  mondayFirst: boolean;
  presets: Array<{ key: string; label: string }> | null;
};
export type EmbedData = {
  value?: FormData["data"] & { slot: string; _html: string };
  key: string;
  label: string;
  tag: string;
  icon: IconData | null;
  attributes: Array<string>;
  fields: { [key: string]: FormFieldData };
  displayEmbedHeader: boolean;
  embedHeaderTitle: string | null;
};
export type EmbedFormData = {
  data: { [key: string]: FormFieldData["value"] };
  fields: { [key: string]: FormFieldData };
  layout: FormLayoutData | null;
};
export type EntityListAuthorizationsData = {
  create: boolean;
  reorder: boolean;
};
export type EntityListConfigData = {
  instanceIdAttribute: string;
  searchable: boolean;
  reorderable: boolean;
  defaultSort: string | null;
  defaultSortDir: string | null;
  hasShowPage: boolean;
  deleteConfirmationText: string;
  deleteHidden: boolean;
  formCreateUrl: string;
  subEntityAttribute: string | null;
  createButtonLabel: string | null;
  quickCreationForm: boolean;
  filters: ConfigFiltersData | null;
  commands: ConfigCommandsData | null;
  state: EntityStateData | null;
};
export type EntityListData = {
  title: string | null;
  authorizations: EntityListAuthorizationsData;
  config: EntityListConfigData;
  fields: Array<EntityListFieldData>;
  data: Array<{ [key: string]: any; _meta: EntityListItemMeta }>;
  filterValues: FilterValuesData;
  query: EntityListQueryParamsData | null;
  entities: Array<EntityListSubEntityData> | null;
  meta: PaginatorMetaData | null;
  pageAlert: PageAlertData | null;
};
export type EntityListFieldData = {
  type: "text" | "state" | "badge";
  key: string;
  label: string;
  sortable: boolean;
  width: string | null;
  hideOnXS: boolean;
  html: boolean | null;
  tooltip: string | null;
};
export type EntityListItemMeta = {
  url: string | null;
  authorizations: InstanceAuthorizationsData;
};
export type EntityListQueryParamsData = {
  search?: string;
  page?: number;
  sort?: string;
  dir?: "asc" | "desc";
} & {
  [filterKey: string]: string;
};
export type EntityListSubEntityData = {
  key: string;
  entityKey: string;
  label: string;
  icon: IconData | null;
  formCreateUrl: string | null;
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
    data: { figure: string; unit: string | null; evolution: string | null };
  };
  key: string;
  type: "figure";
  title: string | null;
  link: string | null;
};
export type FilterData =
  | AutocompleteRemoteFilterData
  | CheckFilterData
  | DateRangeFilterData
  | SelectFilterData;
export type FilterType =
  | "autocompleteRemote"
  | "select"
  | "daterange"
  | "check";
export type FilterValuesData = {
  current: { [key: string]: any };
  default: { [key: string]: any };
  valuated: { [key: string]: boolean };
};
export type FormAutocompleteDynamicLocalValuesData = {
  [key: string]:
    | FormAutocompleteDynamicLocalValuesData
    | Array<FormAutocompleteItemData>;
};
export type FormAutocompleteItemData = { [key: string]: any } & {
  _html: string | null;
  _htmlResult: string | null;
};
export type FormAutocompleteLocalFieldData = {
  value?:
    | FormAutocompleteItemData
    | { [locale: string]: FormAutocompleteItemData };
  key: string;
  type: "autocomplete";
  mode: "local";
  itemIdAttribute: string;
  localValues:
    | Array<FormAutocompleteItemData>
    | FormAutocompleteDynamicLocalValuesData;
  placeholder: string | null;
  listItemTemplate: string | null;
  resultItemTemplate: string | null;
  templateData: { [key: string]: any } | null;
  searchKeys: Array<string> | null;
  localized: boolean | null;
  dynamicAttributes: Array<FormDynamicAttributeData> | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormAutocompleteRemoteFieldData = {
  value?:
    | FormAutocompleteItemData
    | { [locale: string]: FormAutocompleteItemData };
  key: string;
  type: "autocomplete";
  mode: "remote";
  itemIdAttribute: string;
  searchMinChars: number;
  debounceDelay: number;
  remoteEndpoint: string | null;
  callbackLinkedFields: Array<string> | null;
  placeholder: string | null;
  dynamicAttributes: Array<FormDynamicAttributeData> | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormCheckFieldData = {
  value?: boolean;
  key: string;
  type: "check";
  text: string;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormConditionalDisplayData = {
  operator: "and" | "or";
  fields: Array<{ key: string; values: string | boolean | Array<string> }>;
};
export type FormConfigData = {
  isSingle: boolean;
};
export type FormCustomFieldData = {
  value?: any;
  key: string;
  type: string;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormData = {
  title: string | null;
  authorizations: InstanceAuthorizationsData;
  config: FormConfigData;
  data: { [key: string]: FormFieldData["value"] };
  fields: { [key: string]: FormFieldData };
  layout: FormLayoutData;
  locales: Array<string>;
  pageAlert: PageAlertData | null;
};
export type FormDateFieldData = {
  value: string | null;
  key: string;
  type: "date";
  hasDate: boolean;
  hasTime: boolean;
  minTime: string;
  maxTime: string;
  stepTime: number;
  mondayFirst: boolean;
  language: string;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormDynamicAttributeData = {
  name: string;
  type: "map" | "template";
  path: Array<string> | null;
  default: string | null;
};
export type FormDynamicOptionsData = {
  [key: string]:
    | FormDynamicOptionsData
    | Array<{ id: string | number; label: string }>;
};
export type FormEditorFieldData = {
  value?: {
    text: string | { [locale: string]: string | null } | null;
    uploads?: {
      [id: string]: { file: FormUploadFieldValueData; legend?: string | null };
    };
    embeds?: { [embedKey: string]: { [id: string]: EmbedData["value"] } };
  };
  key: string;
  type: "editor";
  minHeight: number;
  markdown: boolean;
  inline: boolean;
  showCharacterCount: boolean;
  uploads: FormEditorFieldUploadData | null;
  embeds: { [embedKey: string]: EmbedData };
  toolbar: Array<FormEditorToolbarButton>;
  maxHeight: number | null;
  maxLength: number | null;
  placeholder: string | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
  localized: boolean | null;
};
export type FormEditorFieldUploadData = {
  fields: { file: FormUploadFieldData; legend: FormTextFieldData | null };
  layout: FormLayoutData;
};
export type FormEditorToolbarButton =
  | "bold"
  | "italic"
  | "highlight"
  | "small"
  | "bullet-list"
  | "ordered-list"
  | "link"
  | "heading-1"
  | "heading-2"
  | "heading-3"
  | "code"
  | "blockquote"
  | "upload"
  | "upload-image"
  | "horizontal-rule"
  | "table"
  | "iframe"
  | "html"
  | "code-block"
  | "superscript"
  | "undo"
  | "redo"
  | "|";
export type FormFieldData =
  | FormAutocompleteLocalFieldData
  | FormAutocompleteRemoteFieldData
  | FormCheckFieldData
  | FormDateFieldData
  | FormEditorFieldData
  | FormGeolocationFieldData
  | FormHtmlFieldData
  | FormListFieldData
  | FormNumberFieldData
  | FormSelectFieldData
  | FormTagsFieldData
  | FormTextFieldData
  | FormTextareaFieldData
  | FormUploadFieldData;
export type FormFieldType =
  | "autocomplete"
  | "check"
  | "date"
  | "editor"
  | "geolocation"
  | "html"
  | "list"
  | "number"
  | "select"
  | "tags"
  | "text"
  | "textarea"
  | "upload";
export type FormGeolocationFieldData = {
  value?: { lat: number; lng: number } | null;
  key: string;
  type: "geolocation";
  geocoding: boolean;
  displayUnit: "DD" | "DMS";
  zoomLevel: number;
  mapsProvider:
    | { name: "gmaps"; options: { apiKey: string; mapId: string } }
    | { name: "osm"; options?: { tilesUrl: string } };
  geocodingProvider:
    | { name: "gmaps"; options: { apiKey: string } }
    | { name: "osm" };
  initialPosition: { lng: number; lat: number };
  boundaries: {
    ne: { lat: number; lng: number };
    sw: { lat: number; lng: number };
  };
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormHtmlFieldData = {
  value?: { [key: string]: any } | null;
  key: string;
  type: "html";
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormLayoutColumnData = {
  size: number;
  fields: Array<Array<LayoutFieldData | FormLayoutFieldsetData>>;
};
export type FormLayoutData = {
  tabbed: boolean;
  tabs: Array<FormLayoutTabData>;
};
export type FormLayoutFieldsetData = {
  legend: string;
  fields: Array<Array<LayoutFieldData>>;
};
export type FormLayoutTabData = {
  title: string;
  columns: Array<FormLayoutColumnData>;
};
export type FormListFieldData = {
  value?: Array<{
    [key: string]: Exclude<FormFieldData["value"], FormListFieldData>;
  }> | null;
  key: string;
  type: "list";
  addable: boolean;
  removable: boolean;
  sortable: boolean;
  itemIdAttribute: string;
  itemFields: { [key: string]: FormFieldData };
  addText: string;
  maxItemCount: number | null;
  bulkUploadField: string | null;
  bulkUploadLimit: number | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormNumberFieldData = {
  value: number | null;
  key: string;
  type: "number";
  step: number;
  showControls: boolean;
  min: number | null;
  max: number | null;
  placeholder: string | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormSelectFieldData = {
  value?: string | number | Array<string | number> | null;
  key: string;
  type: "select";
  options:
    | Array<{
        id: string | number;
        label: string | { [locale: string]: string };
      }>
    | FormDynamicOptionsData;
  multiple: boolean;
  showSelectAll: boolean;
  clearable: boolean;
  display: "list" | "dropdown";
  inline: boolean;
  dynamicAttributes: Array<FormDynamicAttributeData> | null;
  maxSelected: number | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormTagsFieldData = {
  value: Array<{ id: string | number; label: string }> | null;
  key: string;
  type: "tags";
  creatable: boolean;
  createText: string;
  options: Array<{ id: string | number; label: string }>;
  maxTagCount: number | null;
  placeholder: string | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormTextFieldData = {
  value: string | null | { [locale: string]: string | null };
  key: string;
  type: "text";
  inputType: "text" | "password" | "email" | "tel" | "url";
  placeholder: string | null;
  maxLength: number | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormTextareaFieldData = {
  value: string | null | { [locale: string]: string | null };
  key: string;
  type: "textarea";
  rows: number | null;
  placeholder: string | null;
  maxLength: number | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormUploadFieldData = {
  value: FormUploadFieldValueData | null;
  key: string;
  type: "upload";
  imageCropRatio: [number, number];
  imageTransformable: boolean;
  imageCompactThumbnail: boolean;
  imageTransformKeepOriginal: boolean | null;
  imageTransformableFileTypes: Array<string> | null;
  allowedExtensions: Array<string> | null;
  maxFileSize: number | null;
  validationRule: Array<string> | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormUploadFieldValueData = {
  id: number | null;
  name: string;
  disk: string;
  path: string;
  mime_type: string;
  size: number;
  thumbnail: string | null;
  uploaded: boolean | null;
  transformed: boolean | null;
  not_found: boolean | null;
  exists: boolean | null;
  filters: {
    crop: { width: number; height: number; x: number; y: number };
    rotate: { angle: number };
  } | null;
  nativeFile?: File;
};
export type GlobalFiltersData = {
  config: { filters: ConfigFiltersData };
  filterValues: FilterValuesData;
};
export type GlobalSearchData = {
  config: { placeholder: string };
};
export type GraphWidgetData = {
  value?: {
    key: string;
    datasets: Array<{ label: string; data: number[]; color: string }>;
    labels: string[];
  };
  key: string;
  type: "graph";
  title: string | null;
  display: GraphWidgetDisplay;
  showLegend: boolean;
  minimal: boolean;
  ratioX: number | null;
  ratioY: number | null;
  height: number | null;
  dateLabels: boolean;
  options: { curved: boolean; horizontal: boolean };
};
export type GraphWidgetDisplay = "bar" | "line" | "pie";
export type IconData = {
  svg: string | null;
  name: string | null;
};
export type InstanceAuthorizationsData = {
  view: boolean;
  create: boolean;
  update: boolean;
  delete: boolean;
};
export type InstanceSelectionMode = "required" | "allowed";
export type LayoutFieldData = {
  key: string;
  size: number;
  item: Array<Array<LayoutFieldData>> | null;
};
export type LogoData = {
  svg: string | null;
  url: string;
};
export type MenuData = {
  items: Array<MenuItemData>;
  userMenu: UserMenuData;
  isVisible: boolean;
};
export type MenuItemData = {
  icon: IconData | null;
  label: string | null;
  badge: string | null;
  badgeTooltip: string | null;
  badgeUrl: string | null;
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
  value?: {
    key: string;
    data: Array<{ label: string; url?: string; count?: number }>;
  };
  key: string;
  type: "list";
  title: string | null;
  html: boolean;
  link: string | null;
};
export type PageAlertData = {
  level: PageAlertLevel;
  text: string;
  buttonLabel: string | null;
  buttonUrl: string | null;
};
export type PageAlertLevel =
  | "danger"
  | "info"
  | "primary"
  | "secondary"
  | "warning";
export type PaginatorMetaData = {
  current_page: number;
  first_page_url: string;
  from: number | null;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  links: Array<{ url: string | null; label: string; active: boolean }>;
  last_page: number | null;
  last_page_url: string | null;
  total: number | null;
};
export type PanelWidgetData = {
  value?: { key: string; data: { [key: string]: any }; html: string };
  key: string;
  type: "panel";
  title: string | null;
  link: string | null;
};
export type SearchResultLinkData = {
  link: string;
  label: string;
  detail: string | null;
};
export type SearchResultSetData = {
  label: string;
  resultLinks: Array<SearchResultLinkData>;
  icon: IconData | null;
  emptyStateLabel: string | null;
  validationErrors: Array<string>;
  hideWhenEmpty: boolean;
};
export type SelectFilterData = {
  value?: number | string | Array<number | string> | null;
  key: string;
  label: string | null;
  type: "select";
  multiple: boolean;
  required: boolean;
  values: Array<{ id: string | number } & { [key: string]: any }>;
  master: boolean;
  searchable: boolean;
  searchKeys: Array<any>;
};
export type SessionData = {
  _token: string;
  status: string | null;
  status_title: string | null;
  status_level: SessionStatusLevel | null;
};
export type SessionStatusLevel = "error" | "success";
export type ShowConfigData = {
  deleteConfirmationText: string;
  isSingle: boolean;
  commands: ConfigCommandsData | null;
  titleAttribute: string | null;
  breadcrumbAttribute: string | null;
  editButtonLabel: string | null;
  state: EntityStateData | null;
};
export type ShowCustomFieldData = {
  value?: any;
  key: string;
  type: string;
  emptyVisible: boolean;
};
export type ShowData = {
  title: string | { [locale: string]: string } | null;
  authorizations: InstanceAuthorizationsData;
  config: ShowConfigData;
  data: { [key: string]: ShowFieldData["value"] };
  fields: { [key: string]: ShowFieldData };
  layout: ShowLayoutData;
  locales: Array<string> | null;
  pageAlert: PageAlertData | null;
};
export type ShowEntityListFieldData = {
  value?: null;
  key: string;
  type: "entityList";
  emptyVisible: boolean;
  entityListKey: string;
  hiddenCommands: { instance: Array<string>; entity: Array<string> };
  showEntityState: boolean;
  showReorderButton: boolean;
  showCreateButton: boolean;
  showSearchField: boolean;
  showCount: boolean;
  endpointUrl: string;
  label: string | null;
  hiddenFilters: { [key: string]: any } | null;
};
export type ShowFieldData =
  | ShowEntityListFieldData
  | ShowFileFieldData
  | ShowListFieldData
  | ShowPictureFieldData
  | ShowTextFieldData;
export type ShowFieldType =
  | "file"
  | "html"
  | "list"
  | "picture"
  | "text"
  | "entityList";
export type ShowFileFieldData = {
  value?: {
    disk: string;
    name: string;
    path: string;
    thumbnail: string;
    size: number;
    mime_type: string;
  };
  key: string;
  type: "file";
  emptyVisible: boolean;
  label: string | null;
};
export type ShowLayoutColumnData = {
  size: number;
  fields: Array<Array<LayoutFieldData>>;
};
export type ShowLayoutData = {
  sections: Array<ShowLayoutSectionData>;
};
export type ShowLayoutSectionData = {
  key: string | null;
  title: string;
  collapsable: boolean;
  columns: Array<ShowLayoutColumnData>;
};
export type ShowListFieldData = {
  value?: Array<{ [key: string]: ShowFieldData["value"] }>;
  key: string;
  type: "list";
  emptyVisible: boolean;
  itemFields: { [key: string]: ShowFieldData };
  label: string | null;
};
export type ShowPictureFieldData = {
  value?: string;
  key: string;
  type: "picture";
  emptyVisible: boolean;
};
export type ShowTextFieldData = {
  value?: {
    text: string | { [locale: string]: string | null } | null;
    uploads?: {
      [id: string]: { file: FormUploadFieldValueData; legend?: string | null };
    };
    embeds?: { [embedKey: string]: { [id: string]: EmbedData["value"] } };
  };
  key: string;
  type: "text";
  emptyVisible: boolean;
  html: boolean;
  localized: boolean | null;
  collapseToWordCount: number | null;
  embeds: { [key: string]: EmbedData } | null;
  label: string | null;
};
export type UserData = {
  name: string | null;
  email: string | null;
  avatar: string | null;
};
export type UserMenuData = {
  items: Array<MenuItemData>;
};
export type WidgetData =
  | FigureWidgetData
  | GraphWidgetData
  | OrderedListWidgetData
  | PanelWidgetData;
export type WidgetType = "figure" | "graph" | "list" | "panel";
