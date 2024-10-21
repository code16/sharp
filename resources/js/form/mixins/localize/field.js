
export default {
    inject: {
        $form: {
            default:() => ({})
        },
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
