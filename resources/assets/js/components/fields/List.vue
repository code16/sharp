<template>
    <div class="SharpList">
        <button v-if="sortable" type="button" class="btn SharpList__toggle-drag-btn">Réarranger...</button>
        <draggable :options="dragOptions" :list="list" class="list-group">
            <li v-for="listItemValue in list" class="list-group-item">
                <sharp-fields-layout :layout="fieldLayout.item">
                    <template scope="itemFieldLayout">
                        <sharp-field-display :field-key="itemFieldLayout.key"
                                             :context-fields="itemFields"
                                             :context-data="listItemValue">
                        </sharp-field-display>
                    </template>
                </sharp-fields-layout>
            </li>
        </draggable>
        <button type="button" class="btn btn-primary">{{addText}}</button>
    </div>
</template>
<script>
    import Draggable from 'vuedraggable';
    import FieldDisplay from '../FieldDisplay';
    import FieldContainer from '../FieldContainer';

    export default {

        inject:['layout'],

        component: {
            Draggable,
            [FieldDisplay.name]:FieldDisplay,
            [FieldContainer.name]:FieldContainer
        },

        props: {
            fieldLayout: Object,

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
                type: Array,
                required: true,
            },
            collapsedItemTemplate: String,
            maxItemCount: Number,

            value: Array
        },
        data() {
            return {
                dragActive:this.sortable
            }
        },
        computed: {
            list: {
                get() {
                    return this.value;
                },
                set(newlist) {
                    this.$emit('input', newlist);
                }
            },
            dragOptions() {
                return {
                    disabled:this.dragActive
                };
            }
        }
    }
</script>
