<template>
    <div class="SharpList" :class="{ 'SharpList--sort': dragActive }">
        <template v-if="sortable && list.length > 1">
            <button type="button"
                    class="SharpButton SharpButton--ghost SharpList__sort-button"
                    :class="{'SharpButton--active':dragActive}"
                    :data-inactive-text="l('form.list.sort_button.inactive')"
                    :data-active-text="l('form.list.sort_button.active')"
                    @click="toggleDrag">
                <svg class="SharpButton__icon" width='24' height='22' viewBox='0 0 24 22' fill-rule='evenodd'>
                    <path d='M20 14V0h-4v14h-4l6 8 6-8zM4 8v14h4V8h4L6 0 0 8z'></path>
                </svg>
            </button>
        </template>
        <draggable :options="dragOptions" :list="list" ref="draggable">
            <transition-group name="expand" tag="div">
                <div v-for="(listItemData, i) in list" :key="listItemData[indexSymbol]"
                    class="SharpList__item"
                    :class="{'SharpList__item--collapsed':dragActive}"
                >
                    <div class="SharpModule__inner">
                        <div class="SharpModule__content">

                            <template v-if="dragActive && collapsedItemTemplate">
                                <sharp-template name="CollapsedItem" :template="collapsedItemTemplate" :template-data="collapsedItemData(listItemData)"></sharp-template>
                            </template>

                            <template v-else>
                                <sharp-list-item :layout="fieldLayout.item" :error-identifier="i">
                                    <template slot-scope="itemFieldLayout">
                                        <sharp-field-display
                                            :field-key="itemFieldLayout.key"
                                            :context-fields="updatedItemFields"
                                            :context-data="listItemData"
                                            :error-identifier="itemFieldLayout.key"
                                            :config-identifier="itemFieldLayout.key"
                                            :update-data="update(i)"
                                            :locale="listItemData._fieldsLocale[itemFieldLayout.key]"
                                            @locale-change="(key, value)=>updateLocale(i, key, value)"
                                        />
                                    </template>
                                </sharp-list-item>
                                <button v-if="!disabled && removable" class="SharpButton SharpButton--danger SharpButton--sm mt-3" @click="remove(i)">{{ l('form.list.remove_button') }}</button>
                            </template>

                            <!-- Full size div use to handle the item when drag n drop (c.f draggable options) -->
                            <div v-if="dragActive" class="SharpList__overlay-handle"></div>
                        </div>
                    </div>
                    <div v-if="!disabled && showInsertButton && i<list.length-1" class="SharpList__new-item-zone">
                        <button class="SharpButton SharpButton--secondary SharpButton--sm" @click="insertNewItem(i, $event)">{{ l('form.list.insert_button') }}</button>
                    </div>
                </div>
            </transition-group><!-- Important comment, do not remove
         --><template slot="footer">
                <button v-if="!disabled && showAddButton" type="button" :key="-1"
                        class="SharpButton SharpButton--secondary SharpList__add-button"
                        @click="add">{{addText}}</button>
            </template>
        </draggable>
        <em v-if="readOnly && !list.length" class="SharpList__empty-alert">{{l('form.list.empty')}}</em>
    </div>
</template>
<script>
    import Draggable from 'vuedraggable';
    import SharpListItem from './ListItem';
    import SharpTemplate from '../../../Template';

    import { Localization, ReadOnlyFields } from '../../../../mixins';
    import localize from '../../../../mixins/localize/form';


    export default {
        name: 'SharpList',

        inject: ['$form'],

        mixins: [ Localization, ReadOnlyFields('itemFields'), localize('itemFields') ],

        components: {
            Draggable,
            SharpListItem,
            SharpTemplate
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
            readOnly:Boolean,
            locale:String
        },
        data() {
            return {
                list: [],
                itemFieldsLocale: [],
                dragActive: false,
                lastIndex: 0
            }
        },
        computed: {
            disabled() {
                return this.readOnly || this.dragActive;
            },
            updatedItemFields() {
                if(this.readOnly || this.dragActive) {
                    return this.readOnlyFields;
                }
                return this.itemFields;
            },
            dragOptions() {
                return { disabled:!this.dragActive, handle: '.SharpList__overlay-handle' };
            },
            showAddButton() {
                return this.addable && (this.list.length<this.maxItemCount || !this.maxItemCount);
            },
            showInsertButton() {
                return this.showAddButton && this.sortable;
            },
            itemFieldsKeys() {
                return Object.keys(this.itemFields)
            },
            dragIndexSymbol() {
                return Symbol('dragIndex');
            },
            indexSymbol() {
                return Symbol('index');
            },
        },
        methods: {
            indexedList() {
                return (this.value||[]).map((v,i) => this.withLocale({
                    [this.indexSymbol]:i, ...v
                }));
            },
            createItem() {
                return this.itemFieldsKeys.reduce((res, fieldKey) => {
                    res[fieldKey] = null;
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
                return (key, value) => {
                    const item = this.list[i];
                    this.$set(item, key, this.fieldLocalizedValue(key, value, { ...item }, item._fieldsLocale));
                }
            },
            updateLocale(i, key, value) {
                this.$set(this.list[i]._fieldsLocale, key, value);
            },
            collapsedItemData(itemData) {
                return {$index:itemData[this.dragIndexSymbol], ...itemData};
            },
            toggleDrag() {
                this.dragActive = !this.dragActive;
                this.list.forEach((item,i) => item[this.dragIndexSymbol] = i);
            },
            withLocale(item) {
                return {
                    ...item, _fieldsLocale: this.defaultFieldLocaleMap({
                        fields: this.itemFields,
                        locales: this.$form.locales
                    })
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
