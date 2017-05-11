<template>
    <div class="SharpList">
        <div v-if="sortable && list.length" class="text-right">
            <button type="button" class="btn btn-outline-primary" :class="{active:dragActive}" @click="toggleDrag">
                {{dragActive ? 'Ok' : 'Trier'}}
            </button>
        </div>
        <draggable :options="dragOptions" :list="list" class="list-group">
            <li v-for="(listItemData, i) in list" class="SharpList__item list-group-item" :class="{'SharpList__item--collapsed':collapsed}">
                <template v-if="collapsed">
                    <sharp-template :field-key="fieldKey"
                                    :template-data="collapsedItemData(listItemData)"
                                    name="collapsedItem">
                    </sharp-template>
                </template>
                <template v-else>
                    <sharp-fields-layout :layout="fieldLayout.item">
                        <template scope="itemFieldLayout">
                            <sharp-field-display :field-key="itemFieldLayout.key"
                                                 :context-fields="itemFields"
                                                 :context-data="listItemData"
                                                 :update-data="update(i)">
                            </sharp-field-display>
                        </template>
                    </sharp-fields-layout>
                    <button class="btn-link" @click="remove(i)">Supprimer</button>
                </template>
            </li>
        </draggable>
        <button v-show="showAddButton" type="button" class="SharpList__add-button btn btn-secondary" @click="add">{{addText}}</button>
    </div>
</template>
<script>
   import Draggable from 'vuedraggable';
   import FieldsLayout from '../FieldsLayout';
   import Template from '../Template';

    export default {
        name: 'SharpList',


        components: {
            Draggable,
            [FieldsLayout.name]:FieldsLayout,
            [Template.name]:Template
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
            removeText: {
                type:String,
                default:"Supprimer l'élément"
            },
            itemFields: {
                type: Object,
                required: true,
            },
            collapsedItemTemplate: String,
            maxItemCount: Number,
        },
        data() {
            return {
                list:this.value,
                dragActive:false,
            }
        },
        computed: {
            dragOptions() {
                return {
                    disabled:!this.dragActive
                };
            },
            collapsed() {
                return this.dragActive;
            },
            showAddButton() {
                return !this.dragActive && this.list.length<this.maxItemCount;
            },
            indexSymbol() {
                return Symbol('index');
            }
        },
        methods: {
            createItem() {
                return Object.keys(this.itemFields).reduce((res, itemKey) => {
                    res[itemKey] = null;
                    return res;
                },{});
            },
            add() {
                this.list.push(this.createItem());
            },
            remove(i) {
                this.list.splice(i,1);
            },
            update(i) {
                return (key, value) => {
                    this.list[i][key] = value;
                }
            },
            collapsedItemData(itemData) {
                return {$index:itemData[this.indexSymbol], ...itemData};
            },
            toggleDrag() {
                this.dragActive = !this.dragActive;
                this.list.forEach((item,i)=> {
                    item[this.indexSymbol] = i;
                    return item;
                });
            }
        }
    }
</script>
