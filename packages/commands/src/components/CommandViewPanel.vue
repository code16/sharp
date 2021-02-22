<template>
    <div>
        <div class="SharpViewPanel__glasspane" v-show="visible" @click="handleBackdropClicked"></div>
        <transition
            enter-class="SharpViewPanel--collapsed"
            enter-active-class="SharpViewPanel--expanding"
            enter-to-class="SharpViewPanel--expanded"
            leave-class="SharpViewPanel--expanded"
            leave-active-class="SharpViewPanel--collapsing"
            leave-to-class="SharpViewPanel--collapsed"
        >
            <template v-if="visible">
                <div class="SharpViewPanel">
                    <iframe src="about:blank" v-srcdoc="content" sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-modals allow-downloads"></iframe>
                </div>
            </template>
        </transition>
    </div>
</template>

<script>

    export default {
        name: 'SharpViewPanel',
        props: {
            content: String
        },
        computed:{
            visible() {
                return !!this.content;
            },
        },
        methods: {
            handleBackdropClicked() {
                this.$emit('close');
            },
        },
        directives: {
            srcdoc: {
                inserted(el, { value }) {
                    el.contentWindow.document.write(value);
                }
            }
        },
    }
</script>
