<script>
    import Form from './Form';

    export default {
        name:'SharpEmbeddedForm',
        extends:Form,
        props: {
            submitButton: String,
            action: String,
        },
        computed: {
            apiPath() {
                return this.action;
            }
        },
        methods: {
            init() {
                if(!this.test)
                    this.getForm();

                if(this.submitButton) {
                    document.querySelector(this.submitButton).addEventListener('click',e => {
                        this.postForm();
                    });
                }
            }
        }
    }
</script>