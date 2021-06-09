<template>
    <div class="SharpMarkdown">
        <div class="card">
            <div class="card-header">
                <MenuBar :editor="editor" :toolbar="toolbar" />
            </div>
        </div>
    </div>
</template>

<script>
    import MenuBar from "./MenuBar";
    export default {
        components: {
            MenuBar,
        },
        props: {
            id: String,
            value: {
                type: Object,
                default: ()=>({})
            },
            placeholder: String,
            toolbar: Array,
            height: {
                type: Number,
                default: 300
            },
            innerComponents: Object,

            readOnly: Boolean,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
        },
        data() {
            return {
                editor: null,
            }
        }
    }
</script>
