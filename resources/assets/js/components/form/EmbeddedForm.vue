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
            setupActions() { void(0) },
            init() {
                if(!this.test)
                    this.get();

                if(this.submitButton) {
                    document.querySelector(this.submitButton).addEventListener('click',e => {
                        this.post();
                    });
                }
            }
        }
    }
</script>