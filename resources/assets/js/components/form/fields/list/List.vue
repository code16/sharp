<template>
    <div class="SharpList">
        <template v-if="sortable && list.length > 1">
            <button type="button" class="SharpButton SharpButton--secondary SharpList__sort-button" :class="{active:dragActive}" @click="toggleDrag">
                {{dragActive ? 'Ok' : 'Trier'}}
            </button>
        </template>
        <draggable :options="dragOptions" :list="list">
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
                                    <template scope="itemFieldLayout">
                                        <sharp-field-display :field-key="itemFieldLayout.key"
                                                             :context-fields="updatedItemFields"
                                                             :context-data="listItemData"
                                                             :error-identifier="itemFieldLayout.key"
                                                             :update-data="update(i)"
                                                             :locale="locale">
                                        </sharp-field-display>
                                    </template>
                                </sharp-list-item>
                                <button v-if="!disabled && removable" class="SharpButton SharpButton--danger SharpButton--sm" @click="remove(i)">Supprimer</button>
                            </template>
                        </div>
                    </div>
                    <div v-if="!disabled && showAddButton && i<list.length-1" class="SharpList__new-item-zone">
                        <button class="SharpButton SharpButton--secondary" @click="insertNewItem(i)">Insert</button>
                    </div>
                </div>
                <button v-if="!disabled && showAddButton" type="button" :key="-1"
                        class="SharpButton SharpButton--secondary SharpList__add-button" style=""
                        @click="add">{{addText}}</button>
            </transition-group>
        </draggable>
    </div>
</template>
<script>
    import Draggable from 'vuedraggable';
   import ListItem from './ListItem';
   import Template from '../../../Template';

    const noop = ()=>{};


    export default {
        name: 'SharpList',

        inject:['$form'],

        components: {
            Draggable,
            [ListItem.name]:ListItem,
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
                list:[],
                dragActive: false,
                lastIndex: 0,

                transitionActive: false,
            }
        },
        watch: {
            locale() {
                if(this.value == null) {
                    this.initList();
                }
                else this.list = this.value;
            }
        },
        computed: {
            readOnlyItemFields() {
                console.log('readOnly item fields');
                let res = JSON.parse(JSON.stringify(this.itemFields));
                for(let fieldKey of Object.keys(res)) {
                    res[fieldKey].readOnly = true;
                }
                return res;
            },
            disabled() {
                return this.readOnly || this.dragActive;
            },
            updatedItemFields() {
                if(this.readOnly || this.dragActive) {
                    return this.readOnlyItemFields;
                }
                return this.itemFields;
            },
            dragOptions() {
                return { disabled:!this.dragActive };
            },
            showAddButton() {
                return this.addable && (this.list.length<this.maxItemCount || !this.maxItemCount);
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
                return (this.value||[]).map((v,i)=>({[this.indexSymbol]:i,...v}));
            },
            createItem() {
                return Object.keys(this.itemFields).reduce((res, itemKey) => {
                    if(this.$form.localized && this.itemFields[itemKey].localized) {
                        res[itemKey] = this.$form.config.locales.reduce((res, l)=>{
                            res[l] = null;
                            return res;
                        },{});
                    }
                    else res[itemKey] = null;
                    return res;
                },{
                    [this.itemIdAttribute]:null,
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
                    if(this.itemFields[key].localized) {
                        this.list[i][key][this.locale] = value;
                    }
                    else this.list[i][key] = value;
                }
            },
            collapsedItemData(itemData) {
                return {$index:itemData[this.dragIndexSymbol], ...itemData};
            },
            toggleDrag() {
                this.dragActive = !this.dragActive;
                this.list.forEach((item,i) => item[this.dragIndexSymbol] = i);
            },

            initList() {
                this.list = this.indexedList();
                this.lastIndex = this.list.length;
                // make value === list, to update changes
                this.$emit('input', this.list);
            },

            beforeTransition(el,done) {
                console.log(el);
            }

        },
        created() {
            this.initList();
        }
    }
</script>
