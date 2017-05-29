<template>
    <div class="SharpList">
        <div v-if="sortable && list.length" class="text-right">
            <button type="button" class="btn btn-outline-primary" :class="{active:dragActive}" @click="toggleDrag">
                {{dragActive ? 'Ok' : 'Trier'}}
            </button>
        </div>
        <draggable :options="dragOptions" :list="list" class="list-group">
            <transition-group name="expand" tag="div">
                <li v-for="(listItemData, i) in list" :key="listItemData[indexSymbol]"
                    class="SharpList__item list-group-item" :class="{'SharpList__item--collapsed':collapsed}">
                    <template v-if="collapsed">
                        <sharp-template name="collapsedItem" :template="collapsedItemTemplate" :template-data="collapsedItemData(listItemData)"></sharp-template>
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
                        <div v-if="i<list.length-1 && showAddButton" class="SharpList__new-item-zone">
                            <button class="btn btn-secondary" @click="insertNewItem(i)">+</button>
                        </div>
                    </template>
                </li>
                <button v-if="showAddButton" type="button" :key="-1"
                        class="SharpList__add-button btn btn-secondary"
                        @click="add">{{addText}}</button>
            </transition-group>
        </draggable>
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
            maxItemCount: Number
        },
        data() {
            return {
                list:[],
                dragActive:false,
                lastIndex: this.value.length,
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
            dragIndexSymbol() {
                return Symbol('dragIndex');
            },
            indexSymbol() {
                return Symbol('index');
            }
        },
        methods: {
            indexedList() {
                return this.value.map((v,i)=>({[this.indexSymbol]:i,...v}));
            },
            createItem() {
                return Object.keys(this.itemFields).reduce((res, itemKey) => {
                    res[itemKey] = null;
                    return res;
                },{
                    [this.indexSymbol]:this.lastIndex++
                });
            },
            insertNewItem(i) {
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
                    this.list[i][key] = value;
                }
            },
            collapsedItemData(itemData) {
                return {$index:itemData[this.dragIndexSymbol], ...itemData};
            },
            toggleDrag() {
                this.dragActive = !this.dragActive;
                this.list.forEach((item,i) => item[this.dragIndexSymbol] = i);
            }
        },
        created() {
            this.list = this.indexedList();
        }
    }
</script>
