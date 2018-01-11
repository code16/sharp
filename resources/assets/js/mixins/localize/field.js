import Vue from 'vue';
import { localeObject } from "./utils";

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
        }
    }
}