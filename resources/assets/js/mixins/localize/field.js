import Vue from 'vue';

export default {
    inject: {
        $form: { default:()=>new Vue() }
    },
    props: {
        locale: String,
        localized: Boolean
    },
    computed:{
        locales() {
            return this.$form.locales;
        },
        isLocalized() {
            return this.$form.localized && this.localized;
        }
    }
}