<template>
    <div>
        <draggable :list="list">
            
        </draggable>
        <button type="button" class="btn btn-primary">{{addText}}</button>
    </div>
</template>
<script>
    import Draggable from 'vuedraggable';

    export default {

        component: {
            Draggable
        },

        model: {
            prop: '_list',
            event: 'list-changed'
        },

        props: {
            fieldKey:String,

            addable: Boolean,
            sortable: Boolean,
            removable: Boolean,
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
            itemTemplate: String,
            maxItemCount: Number,

            _list: Array
        },

        computed: {
            list: {
                get() {
                    return this._list;
                }
                set(newlist) {
                    this.$emit('list-changed', newlist);
                }
            }
        }
    }
</script>
