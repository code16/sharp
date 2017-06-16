<script>
    import Multiselect from 'vue-multiselect';
    export default {
        name:'SharpMultiselect',
        functional:true,
        render(h, ctx) {
            return h(Multiselect, ctx.data, [
                h({
                    template:`
                    <svg slot="carret" class="multiselect__select" width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd">
                        <path d="M10 0L5 5 0 0z"></path>
                    </svg>`
                }, {
                    slot:'carret'
                }),
                ...ctx.children
            ])
        }
    }
</script>