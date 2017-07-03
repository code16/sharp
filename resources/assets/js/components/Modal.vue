<script>
    import bModal from './vendor/bootstrap-vue/components/modal';

    export default {
        name:'SharpModal',
        functional: true,

        render(h, ctx) {
            console.log(ctx);
            ctx.data['class'] = {
                ...ctx.data['class'],
                SharpModal: true,
                'SharpModal--error': ctx.props.isError
            };

            return h(bModal, ctx.data, [
                h({
                    name: 'SharpModalTitle',
                    template:`
                    <div>
                        <h5 class="SharpModal__heading">
                            <slot name="title">{{title}}</slot>
                        </h5>
                        <button v-if="!okOnly" class="SharpModal__close" type="button" @click="hide">
                            <svg class="SharpModal__close-icon" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                              <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                            </svg>
                        </button>
                    </div>
                    `,
                    props: { title: String, okOnly: Boolean },
                    methods: {
                        hide() {
                            console.log(this);
                            this.$parent.hide();
                        }
                    }
                }, {
                    slot: 'modal-header',
                    props: ctx.props
                }, [
                    ctx.slots().title
                ]),
                ...ctx.children
            ]);
        }
    }
</script>