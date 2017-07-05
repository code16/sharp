export default {
    inject:['$field'],
    props: {
        focused:Boolean
    },
    watch: {
        focused(focused) {
            if(focused) {
                this.$focusableElm.focus();
            }
        }
    },
    data() {
        return {
            $focusableElm: null,
        }
    },
    methods: {
        setFocusable(elm) {
            this.$focusableElm = elm;
            if(this.$field) {
                this.$focusableElm.addEventListener('blur',_=>{
                    this.$field.$emit('blur');
                });
            }
        }
    }

}