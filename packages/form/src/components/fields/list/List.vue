<template>
    <div class="SharpList" :class="classes">
        <template v-if="showSortButton">
            <Button
                class="SharpList__sort-button"
                text
                small
                :active="dragActive"
                @click="toggleDrag"
            >
                {{ l('form.list.sort_button.inactive') }}
                <svg style="margin-left: .5em" width="1.125em" height="1.125em" viewBox="0 0 24 22" fill-rule="evenodd">
                    <path d="M20 14V0h-4v14h-4l6 8 6-8zM4 8v14h4V8h4L6 0 0 8z"></path>
                </svg>
            </Button>
        </template>
        <Draggable :options="dragOptions" :list="list" ref="draggable">
            <transition-group name="expand" tag="div" class="list-group shadow-sm">
                <template v-for="(listItemData, i) in list">
                    <div class="SharpList__item list-group-item"
                        :class="{'SharpList__item--collapsed': dragActive}"
                        :key="listItemData[indexSymbol]"
                    >
                        <template v-if="dragActive && collapsedItemTemplate">
                            <TemplateRenderer
                                name="CollapsedItem"
                                :template="collapsedItemTemplate"
                                :template-data="collapsedItemData(listItemData)"
                            />
                        </template>

                        <template v-else>
                            <ListItem :layout="fieldLayout.item" :error-identifier="i" v-slot="{ fieldLayout }">
                                <FieldDisplay
                                    :field-key="fieldLayout.key"
                                    :context-fields="transformedFields(i)"
                                    :context-data="listItemData"
                                    :error-identifier="fieldLayout.key"
                                    :config-identifier="fieldLayout.key"
                                    :update-data="update(i)"
                                    :locale="listItemData._fieldsLocale[fieldLayout.key]"
                                    :read-only="isReadOnly"
                                    :list="true"
                                    @locale-change="(key, value)=>updateLocale(i, key, value)"
                                />
                            </ListItem>
                            <template v-if="showRemoveButton">
<!--                                <div class="mt-3 text-center">-->
<!--                                    <Button variant="danger" outline small @click="remove(i)">-->
<!--                                        {{ l('form.list.remove_button') }}-->
<!--                                    </Button>-->
<!--                                </div>-->
                            </template>
                        </template>

                        <!-- Full size div use to handle the item when drag n drop (c.f draggable options) -->
                        <template v-if="dragActive">
                            <div class="SharpList__overlay-handle"></div>
                        </template>
                        <template v-if="showInsertButton && i < list.length-1">
                            <div class="SharpList__new-item-zone">
                                <Button small @click="insertNewItem(i, $event)">
                                    {{ l('form.list.insert_button') }}
                                </Button>
                            </div>
                        </template>
                    </div>
                </template>
            </transition-group>

            <template v-if="showAddButton" v-slot:footer>
                <div :class="{ 'mt-3': list.length > 0 }">
                    <Button class="SharpList__add-button" text block @click="add" :key="-1">
                        ＋ {{ addText }}
                    </Button>
                </div>
            </template>
        </Draggable>
        <template v-if="readOnly && !list.length">
            <em class="SharpList__empty-alert">{{l('form.list.empty')}}</em>
        </template>
    </div>
</template>
<script>
    import Draggable from 'vuedraggable';
    import { TemplateRenderer } from 'sharp/components';
    import { Localization } from 'sharp/mixins';
    import { Button } from "sharp-ui";
    import ListItem from './ListItem';

    import localize from '../../../mixins/localize/form';
    import { transformFields, getDependantFieldsResetData, fieldEmptyValue } from "../../../util";

    export default {
        name: 'SharpList',

        inject: ['$form'],

        mixins: [ Localization,  localize('itemFields') ],

        components: {
            Draggable,
            ListItem,
            Button,
            TemplateRenderer,
        },

        props: {
            fieldKey: String,
            fieldLayout: Object,
            value: Array,

            addable: {
                type:Boolean,
                default: true
            },
            sortable: {
                type: Boolean,
                default: false
            },
            removable: {
                type: Boolean,
                default: false
            },
            addText: {
                type:String,
                default:'Ajouter un élément'
            },
            itemFields: {
                type: Object,
                required: true,
            },
            collapsedItemTemplate: String,
            maxItemCount: Number,

            itemIdAttribute: String,
            readOnly: Boolean,
            locale: [String, Array],
        },
        data() {
            return {
                list: [],
                dragActive: false,
                lastIndex: 0
            }
        },
        watch: {
            'list': 'handleListChanged',
            'locale': 'handleLocaleChanged',
        },
        computed: {
            classes() {
                return {
                    'SharpList--can-sort': this.showSortButton,
                }
            },
            disabled() {
                return this.readOnly || this.dragActive;
            },
            dragOptions() {
                return {
                    disabled:!this.dragActive,
                    handle: '.SharpList__overlay-handle',
                };
            },
            showAddButton() {
                return this.addable &&
                    (this.list.length < this.maxItemCount || !this.maxItemCount) &&
                    !this.disabled;
            },
            showInsertButton() {
                return this.showAddButton && this.sortable && !this.disabled;
            },
            showSortButton() {
                return !this.hasPendingActions && this.sortable && this.list.length > 1;
            },
            showRemoveButton() {
                return this.removable && !this.disabled;
            },
            dragIndexSymbol() {
                return Symbol('dragIndex');
            },
            indexSymbol() {
                return Symbol('index');
            },
            hasPendingActions() {
                return this.$form?.hasUploadingFields(this.fieldKey);
            },
            isReadOnly() {
                return this.readOnly || this.dragActive;
            }
        },
        methods: {
            handleListChanged() {
                this.$emit('locale-change', this.list.map(item => item._fieldsLocale));
            },
            handleLocaleChanged(locale) {
                if(typeof locale === 'string') {
                    this.list.forEach(item => {
                        Object.assign(item, this.withLocale(null, locale));
                    });
                }
            },
            itemData(item) {
                const { id, _fieldsLocale, ...data } = item;
                return data;
            },
            transformedFields(i) {
                const item = this.list[i];
                const data = this.itemData(item);
                return transformFields(this.itemFields, data);
            },
            indexedList() {
                return (this.value||[]).map((v,i) => this.withLocale({
                    [this.indexSymbol]:i, ...v
                }));
            },
            createItem() {
                return Object.entries(this.itemFields).reduce((res, [key, field]) => {
                    res[key] = fieldEmptyValue(field.type);
                    return res;
                }, this.withLocale({
                    [this.itemIdAttribute]:null,
                    [this.indexSymbol]:this.lastIndex++
                }));
            },
            insertNewItem(i, $event) {
                $event.target && $event.target.blur();
                this.list.splice(i+1, 0, this.createItem());
            },
            add() {
                this.list.push(this.createItem());
            },
            remove(i) {
                this.list.splice(i,1);
            },
            update(i) {
                return (key, value, { forced } = {}) => {
                    const item = { ...this.list[i] };
                    const data = {
                        ...(!forced ? getDependantFieldsResetData(this.itemFields, key, () =>
                            this.fieldLocalizedValue(key, null, item, item._fieldsLocale)
                        ) : null),
                        [key]: this.fieldLocalizedValue(key, value, item, item._fieldsLocale),
                    };
                    Object.assign(this.list[i], data);
                }
            },
            updateLocale(i, key, value) {
                this.$set(this.list[i]._fieldsLocale, key, value);
                this.handleListChanged();
            },
            collapsedItemData(itemData) {
                return {$index:itemData[this.dragIndexSymbol], ...itemData};
            },
            toggleDrag() {
                this.dragActive = !this.dragActive;
                this.list.forEach((item,i) => item[this.dragIndexSymbol] = i);
            },
            withLocale(item, locale) {
                return {
                    ...item, _fieldsLocale: this.defaultFieldLocaleMap({
                        fields: this.itemFields,
                        locales: this.$form.locales
                    }, locale)
                };
            },

            initList() {
                this.list = this.indexedList();
                this.lastIndex = this.list.length;
                // make value === list, to update changes
                this.$emit('input', this.list);
            },
        },
        created() {
            this.localized = this.$form.localized;
            this.initList();
        },
    }
</script>
