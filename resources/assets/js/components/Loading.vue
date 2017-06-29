<template>
    <div class="SharpLoading__overlay" v-show="show">
        <!--<transition leave-active-class="SharpLoading&#45;&#45;stop" @after-leave="show=false">-->
            <div class="SharpLoading" v-show="show">
                <svg class="SharpLoading__svg" viewBox="-75 -75 150 150">
                    <circle cx="0" cy="0" r="37.5" />
                </svg>
            </div>
        <!--</transition>-->
    </div>
</template>

<script>
    import Vue from 'vue';
    export default Vue.extend({
        data() {
            return {
                show: false,
                showLoading: true
            }
        },
        watch: {
            show(s) { this.showLoading = s }
        },
        created() {
            this.$on('show', _ => this.show = true);
            this.$on('hide', _ => this.show = false);
        }
    });
</script>